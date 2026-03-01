<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sub = \App\Models\SchoolSubscription::where('status', 'pending')->latest()->first();

if (!$sub) {
    echo "No pending subscription found.\n";
    exit(1);
}

$orderId = $sub->midtrans_order_id;
$statusCode = '200';
$grossAmount = number_format($sub->total_price, 2, '.', '');
$serverKey = config('services.midtrans.server_key');

$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

$payload = [
    'order_id' => $orderId,
    'status_code' => $statusCode,
    'gross_amount' => $grossAmount,
    'signature_key' => $signature,
    'transaction_status' => 'settlement',
    'fraud_status' => 'accept',
];

echo "Simulating webhook for Order ID: {$orderId}\n";
echo "Amount: {$grossAmount}\n";

$response = \Illuminate\Support\Facades\Http::post(url('/api/midtrans/webhook'), $payload);

echo "Response status: " . $response->status() . "\n";
echo "Response body: " . $response->body() . "\n";

