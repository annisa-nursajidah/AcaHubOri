<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SchoolRegistrationController extends Controller
{
    /**
     * Show the school registration form.
     */
    public function showForm(Request $request)
    {
        $plans      = SubscriptionPlan::active()->get();
        $selectedPlan = $request->plan ? SubscriptionPlan::find($request->plan) : null;

        return view('schools.register', compact('plans', 'selectedPlan'));
    }

    /**
     * Process school registration + create Midtrans payment.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'school_name'    => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone'   => 'nullable|string|max:20',
            'school_email'   => 'required|email|unique:schools,email',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'plan_id'        => 'required|exists:subscription_plans,id',
            'total_accounts' => 'required|integer|min:1',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Validate account limits
        if ($validated['total_accounts'] < $plan->min_accounts) {
            return back()->withErrors(['total_accounts' => "Minimum {$plan->min_accounts} akun."])->withInput();
        }
        if ($plan->max_accounts && $validated['total_accounts'] > $plan->max_accounts) {
            return back()->withErrors(['total_accounts' => "Maksimum {$plan->max_accounts} akun."])->withInput();
        }

        $totalPrice = $plan->calculatePrice($validated['total_accounts']);
        $orderId    = 'SCH-' . strtoupper(Str::random(8)) . '-' . time();

        // Create everything in a transaction
        $result = DB::transaction(function () use ($validated, $plan, $totalPrice, $orderId) {
            // 1. Create school (inactive until payment)
            $school = School::create([
                'name'      => $validated['school_name'],
                'address'   => $validated['school_address'],
                'phone'     => $validated['school_phone'],
                'email'     => $validated['school_email'],
                'is_active' => false,
            ]);

            // 2. Create school admin user
            $admin = User::create([
                'name'      => $validated['admin_name'],
                'email'     => $validated['admin_email'],
                'password'  => Hash::make($validated['admin_password']),
                'role'      => 'school_admin',
                'school_id' => $school->id,
            ]);

            // 3. Create subscription (pending)
            $subscription = SchoolSubscription::create([
                'school_id'         => $school->id,
                'plan_id'           => $plan->id,
                'total_accounts'    => $validated['total_accounts'],
                'price_per_account' => $plan->price_per_account,
                'total_price'       => $totalPrice,
                'status'            => 'pending',
                'midtrans_order_id' => $orderId,
            ]);

            return compact('school', 'admin', 'subscription');
        });

        // 4. Create Midtrans Snap token
        $snapToken = $this->createMidtransSnapToken(
            $result['subscription'],
            $result['school'],
            $result['admin']
        );

        if ($snapToken) {
            $result['subscription']->update(['midtrans_snap_token' => $snapToken]);
        }

        return view('schools.payment', [
            'subscription' => $result['subscription'],
            'school'       => $result['school'],
            'snapToken'    => $snapToken,
        ]);
    }

    /**
     * Payment success callback page.
     * Verifies transaction status directly from Midtrans API.
     */
    public function paymentSuccess(Request $request)
    {
        $orderId      = $request->order_id;
        $subscription = SchoolSubscription::where('midtrans_order_id', $orderId)
            ->with('school')
            ->first();

        if (!$subscription) {
            abort(404, 'Order tidak ditemukan.');
        }

        // Jika masih pending, cek status ke Midtrans API langsung
        if ($subscription->status === 'pending') {
            $this->checkAndActivateFromMidtrans($subscription);
            $subscription->refresh();
        }

        return view('schools.payment-success', compact('subscription'));
    }

    /**
     * Cek status transaksi ke Midtrans dan aktifkan jika sudah dibayar.
     */
    private function checkAndActivateFromMidtrans(SchoolSubscription $subscription): void
    {
        $serverKey   = config('services.midtrans.server_key');
        if (!$serverKey) return;

        $isProduction = config('services.midtrans.is_production', false);
        $baseUrl = $isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->get("{$baseUrl}/{$subscription->midtrans_order_id}/status");

            if (!$response->successful()) return;

            $data              = $response->json();
            $transactionStatus = $data['transaction_status'] ?? '';
            $fraudStatus       = $data['fraud_status'] ?? '';

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                if ($fraudStatus === 'accept' || empty($fraudStatus)) {
                    $subscription->activate();
                    \Log::info("Payment success callback: subscription {$subscription->midtrans_order_id} activated.");
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $subscription->update(['status' => 'cancelled']);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans status check failed', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Create Midtrans Snap Token via API.
     */
    private function createMidtransSnapToken(
        SchoolSubscription $subscription,
        School $school,
        User $admin
    ): ?string {
        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey) {
            return null; // Midtrans not configured
        }

        $isProduction = config('services.midtrans.is_production', false);
        $baseUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->post($baseUrl, [
                    'transaction_details' => [
                        'order_id'     => $subscription->midtrans_order_id,
                        'gross_amount' => (int) $subscription->total_price,
                    ],
                    'customer_details' => [
                        'first_name' => $admin->name,
                        'email'      => $admin->email,
                        'phone'      => $school->phone ?? '',
                    ],
                    'item_details' => [
                        [
                            'id'       => 'plan-' . $subscription->plan_id,
                            'price'    => (int) $subscription->price_per_account,
                            'quantity' => $subscription->total_accounts,
                            'name'     => "Paket {$subscription->plan->name} ({$subscription->total_accounts} akun)",
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return $response->json('token');
            }

            \Log::error('Midtrans Snap error', ['response' => $response->body()]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap exception', ['error' => $e->getMessage()]);
        }

        return null;
    }
}
