<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EvictDeadAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'app:evict-dead-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically evict tenants who are late > 3 days from their due date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting eviction process for dead accounts...');
        \App\Models\Transaksi::checkDeadAccounts();
        $this->info('Eviction process completed successfully.');
    }
}
