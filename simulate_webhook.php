<?php
/**
 * simulate_webhook.php
 * Script simulasi notifikasi pembayaran Midtrans (webhook) ke AcaHub.
 *
 * Cara pakai:
 *   php simulate_webhook.php                        → ambil order terbaru dari DB
 *   php simulate_webhook.php SUB-TESTWEBHOOK-12345  → pakai order ID tertentu
 *   php simulate_webhook.php SUB-TESTWEBHOOK-12345 cancel → simulasi pembatalan
 */

require __DIR__ . '/vendor/autoload.php';

$app    = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SchoolSubscription;
use Illuminate\Support\Facades\Http;

// ─── Ambil Order ID ───────────────────────────────────────────────────────────
$orderId        = $argv[1] ?? null;
$transactionType = $argv[2] ?? 'settlement';  // settlement | cancel | expire | deny | pending

if (!$orderId) {
    // Ambil subscription pending terbaru dari DB
    $sub = SchoolSubscription::where('status', 'pending')
        ->latest()
        ->first();

    if (!$sub) {
        echo "❌ Tidak ada subscription berstatus 'pending'. Buat dulu via seed_test_subscription.php\n";
        exit(1);
    }

    $orderId    = $sub->midtrans_order_id;
    $grossAmount = number_format($sub->total_price, 2, '.', '');
    echo "ℹ️  Auto-detect Order ID terbaru: {$orderId}\n";
} else {
    $sub = SchoolSubscription::where('midtrans_order_id', $orderId)->first();
    if (!$sub) {
        echo "❌ Order ID '{$orderId}' tidak ditemukan di database.\n";
        exit(1);
    }
    $grossAmount = number_format($sub->total_price, 2, '.', '');
}

// ─── Konfigurasi ──────────────────────────────────────────────────────────────
$serverKey  = config('services.midtrans.server_key') ?: 'SB-Mid-server-TEST_KEY_ACAHUB_LOCAL';
$webhookUrl = config('app.url') . '/api/midtrans/webhook';
$statusCode = '200';

// Generate signature seperti yang divalidasi Midtrans
$signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

// ─── Payload Webhook ─────────────────────────────────────────────────────────
$fraudStatus = '';
if ($transactionType === 'settlement' || $transactionType === 'capture') {
    $fraudStatus = 'accept';
}

$payload = [
    'transaction_time'   => now()->format('Y-m-d H:i:s'),
    'transaction_status' => $transactionType,
    'transaction_id'     => 'SIM-' . strtoupper(uniqid()),
    'status_message'     => 'midtrans payment notification',
    'status_code'        => $statusCode,
    'signature_key'      => $signatureKey,
    'settlement_time'    => now()->format('Y-m-d H:i:s'),
    'payment_type'       => 'bank_transfer',
    'order_id'           => $orderId,
    'merchant_id'        => 'G123456789',
    'gross_amount'       => $grossAmount,
    'fraud_status'       => $fraudStatus,
    'currency'           => 'IDR',
];

// ─── Kirim Request ────────────────────────────────────────────────────────────
echo "\n===========================================\n";
echo "  Simulasi Webhook Midtrans → AcaHub\n";
echo "===========================================\n";
echo "URL         : {$webhookUrl}\n";
echo "Order ID    : {$orderId}\n";
echo "Gross Amount: Rp " . number_format($sub->total_price, 0, ',', '.') . "\n";
echo "Jenis       : {$transactionType}\n";
echo "Server Key  : " . substr($serverKey, 0, 10) . "...\n";
echo "-------------------------------------------\n";

try {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json',
    ])->post($webhookUrl, $payload);

    echo "✅ Response Status : HTTP {$response->status()}\n";
    echo "✅ Response Body   : " . $response->body() . "\n";

    // Cek result di DB
    $sub->refresh();
    echo "🗄️  Status DB sekarang: {$sub->status}\n";

    if ($transactionType === 'settlement' && $sub->status === 'active') {
        echo "\n🎉 SUKSES! Subscription berhasil diaktifkan via webhook.\n";
        echo "   Aktif hingga: " . ($sub->ends_at ?? '-') . "\n";
    } elseif (in_array($transactionType, ['cancel', 'deny', 'expire']) && $sub->status === 'cancelled') {
        echo "\n✅ Subscription berhasil dibatalkan via webhook.\n";
    } elseif ($response->status() === 403) {
        echo "\n⚠️  Signature tidak valid. Periksa MIDTRANS_SERVER_KEY di .env\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Pastikan server AcaHub berjalan (php artisan serve atau Laragon)\n";
}

echo "===========================================\n";
