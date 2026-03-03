<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FailExpiredOrders extends Command
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
    protected $signature = 'orders:fail-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fail orders that have exceeded the 24h payment window without proof.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = \App\Models\Transaksi::where('status', 'verified')
            ->whereNull('bukti_pembayaran')
            ->where('batas_bayar', '<', now())
            ->get();

        foreach ($expiredOrders as $order) {
            \DB::beginTransaction();
            try {
                $order->update(['status' => 'failed']);

                // RELEASE THE ROOM
                $kamar = \App\Models\Kamar::find($order->id_kamar);
                if ($kamar) {
                    $kamar->update(['status' => 'tersedia']);
                }

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                $this->error("Failed to process order ID {$order->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully processed " . count($expiredOrders) . " expired orders.");
    }
}
