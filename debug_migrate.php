<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Artisan::call('migrate', ['--force' => true]);
    file_put_contents('err_migrasi.log', Artisan::output());
} catch (\Throwable $e) {
    file_put_contents('err_migrasi.log', $e->getMessage() . "\n" . $e->getTraceAsString());
}
