<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Public pricing page.
     */
    public function plans()
    {
        $plans = SubscriptionPlan::active()->get();
        return view('subscriptions.pricing', compact('plans'));
    }

    /**
     * Admin: list all subscriptions.
     */
    public function index()
    {
        $subscriptions = SchoolSubscription::with(['school', 'plan'])
            ->latest()
            ->paginate(15);

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show form to create subscription for a school.
     */
    public function create(Request $request)
    {
        $schools = School::orderBy('name')->get();
        $plans   = SubscriptionPlan::active()->get();
        $selectedSchool = $request->school_id ? School::find($request->school_id) : null;

        return view('subscriptions.create', compact('schools', 'plans', 'selectedSchool'));
    }

    /**
     * Store a new subscription (admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id'      => 'required|exists:schools,id',
            'plan_id'        => 'required|exists:subscription_plans,id',
            'total_accounts' => 'required|integer|min:1',
            'notes'          => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Validate account limits
        if ($validated['total_accounts'] < $plan->min_accounts) {
            return back()->withErrors(['total_accounts' => "Minimum pembelian {$plan->min_accounts} akun."])->withInput();
        }
        if ($plan->max_accounts && $validated['total_accounts'] > $plan->max_accounts) {
            return back()->withErrors(['total_accounts' => "Maksimum pembelian {$plan->max_accounts} akun."])->withInput();
        }

        $subscription = SchoolSubscription::create([
            'school_id'         => $validated['school_id'],
            'plan_id'           => $validated['plan_id'],
            'total_accounts'    => $validated['total_accounts'],
            'price_per_account' => $plan->price_per_account,
            'total_price'       => $validated['total_accounts'] * $plan->price_per_account,
            'status'            => 'pending',
            'midtrans_order_id' => 'SUB-' . strtoupper(Str::random(10)) . '-' . time(),
            'notes'             => $validated['notes'],
        ]);

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Langganan berhasil dibuat. Menunggu pembayaran.');
    }

    /**
     * Show subscription detail.
     */
    public function show(SchoolSubscription $subscription)
    {
        $subscription->load(['school', 'plan']);
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Update subscription status (admin).
     */
    public function updateStatus(Request $request, SchoolSubscription $subscription)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,active,expired,cancelled',
        ]);

        if ($validated['status'] === 'active' && $subscription->status !== 'active') {
            $subscription->activate();
            return back()->with('success', 'Langganan berhasil diaktifkan.');
        }

        $subscription->update(['status' => $validated['status']]);
        return back()->with('success', 'Status langganan berhasil diperbarui.');
    }

    /**
     * Midtrans webhook handler (called via API route).
     */
    public function midtransWebhook(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');

        $payload       = $request->all();
        $orderId       = $payload['order_id'] ?? null;
        $statusCode    = $payload['status_code'] ?? null;
        $grossAmount   = $payload['gross_amount'] ?? null;
        $signatureKey  = $payload['signature_key'] ?? null;

        // Verify signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans webhook: invalid signature', $payload);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $subscription = SchoolSubscription::where('midtrans_order_id', $orderId)->first();
        if (! $subscription) {
            Log::warning('Midtrans webhook: subscription not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? '';

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept' || empty($fraudStatus)) {
                $subscription->activate();
                Log::info("Midtrans: subscription {$orderId} activated");
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $subscription->update(['status' => 'cancelled']);
            Log::info("Midtrans: subscription {$orderId} cancelled");
        } elseif ($transactionStatus === 'pending') {
            $subscription->update(['status' => 'pending']);
        }

        return response()->json(['message' => 'OK']);
    }
}
