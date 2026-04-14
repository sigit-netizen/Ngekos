<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \DB::table('failed_jobs')->count();
echo "Number of failed jobs: " . $count . "\n";

$failed = \DB::table('failed_jobs')->latest()->get();
foreach ($failed as $f) {
    echo "ID: " . $f->id . " - Error: " . substr($f->exception, 0, 200) . "...\n";
}
