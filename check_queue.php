<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \DB::table('jobs')->count();
echo "Number of pending jobs in queue: " . $count . "\n";

$jobs = \DB::table('jobs')->get();
foreach ($jobs as $job) {
    echo "Job ID: " . $job->id . " - Payload: " . substr($job->payload, 0, 100) . "...\n";
}
