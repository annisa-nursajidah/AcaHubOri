<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::connection()->getPdo();
    echo "Koneksi OK\n";
    $tables = DB::select('SHOW TABLES');
    echo "Jumlah tabel: " . count($tables) . "\n";
} catch (\Exception $e) {
    echo "Error DB: " . $e->getMessage() . "\n";
}
