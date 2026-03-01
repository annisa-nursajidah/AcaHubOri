<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\School;
use App\Models\SubscriptionPlan;
use App\Models\SchoolSubscription;

// Buat school dummy jika belum ada
$school = School::firstOrCreate(
    ['email' => 'admin@sman1test.sch.id'],
    [
        'name'      => 'SMA Negeri 1 Test',
        'phone'     => '08123456789',
        'address'   => 'Jl. Pendidikan No. 1, Jakarta',
        'password'  => bcrypt('password123'),
        'is_active' => true,
    ]
);

$plan = SubscriptionPlan::first();
if (!$plan) {
    echo "ERROR: Tidak ada subscription plan. Jalankan seeder dulu.\n";
    exit(1);
}

$orderId    = 'SUB-TESTWEBHOOK-' . time();
$totalPrice = 20 * $plan->price_per_account;

$sub = SchoolSubscription::create([
    'school_id'         => $school->id,
    'plan_id'           => $plan->id,
    'total_accounts'    => 20,
    'price_per_account' => $plan->price_per_account,
    'total_price'       => $totalPrice,
    'status'            => 'pending',
    'midtrans_order_id' => $orderId,
]);

echo "=== Data Subscription Test Berhasil Dibuat ===\n";
echo "School      : {$school->name}\n";
echo "Plan        : {$plan->name}\n";
echo "Order ID    : {$orderId}\n";
echo "Total Price : Rp " . number_format($totalPrice, 0, ',', '.') . "\n";
echo "Status      : {$sub->status}\n";
echo "\n";
echo "Gunakan Order ID dan Total Price di atas untuk menjalankan simulate_webhook.php\n";
