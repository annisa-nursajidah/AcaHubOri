<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sub = App\Models\SchoolSubscription::where('status', 'pending')->latest()->first();
if (!$sub) { echo "Tidak ada subscription pending." . PHP_EOL; exit; }

echo "Mengaktifkan: " . $sub->school->name . " (order: " . $sub->midtrans_order_id . ")" . PHP_EOL;
$sub->activate();
$sub->refresh();
echo "Status     : " . $sub->status . PHP_EOL;
echo "Expires    : " . $sub->expires_at . PHP_EOL;
echo "Total akun : " . $sub->total_accounts . PHP_EOL;
$school = $sub->school->fresh();
echo "is_active  : " . ($school->is_active ? 'YES' : 'NO') . PHP_EOL;
echo "canCreate  : " . ($school->canCreateAccount() ? 'YES (' . $school->totalAccountsQuota() . ' akun)' : 'NO') . PHP_EOL;
