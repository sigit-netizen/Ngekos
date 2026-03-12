<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendJatuhTempoNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-jatuh-tempo-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WhatsApp otomatis untuk penyewa yang mendekati jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fonnteService = app(\App\Services\FonnteService::class);
        $today = now('Asia/Jakarta')->startOfDay();
        $tomorrow = $today->copy()->addDay();

        // Cari transaksi 'paid' yang jatuh temponya hari ini atau besok
        $transaksis = \App\Models\Transaksi::where('status', 'paid')
            ->whereIn('tipe', [\App\Models\Transaksi::TYPE_BOOKING, \App\Models\Transaksi::TYPE_SEWA])
            ->where(function ($q) use ($today, $tomorrow) {
                $q->whereDate('jatuh_tempo', $today)
                    ->orWhereDate('jatuh_tempo', $tomorrow);
            })
            ->with(['user', 'kamar.kos'])
            ->get();

        if ($transaksis->isEmpty()) {
            $this->info('Tidak ada penyewa yang mendekati jatuh tempo hari ini/besok.');
            return;
        }

        foreach ($transaksis as $tx) {
            $user = $tx->user;
            if (!$user || !$user->nomor_wa)
                continue;

            $daysDiff = $today->diffInDays(\Carbon\Carbon::parse($tx->jatuh_tempo)->startOfDay(), false);
            $timeInfo = $daysDiff == 0 ? '*HARI INI*' : '*BESOK*';

            $message = "Halo *{$user->name}*,\n\nIni adalah pengingat bahwa masa sewa kos Anda di *{$tx->kamar->kos->nama_kos}* (Kamar {$tx->kamar->nomor_kamar}) akan berakhir {$timeInfo} (tgl " . \Carbon\Carbon::parse($tx->jatuh_tempo)->format('d-m-Y') . ").\n\nSilakan lakukan pembayaran melalui aplikasi untuk memperpanjang masa sewa agar tidak terkena denda atau penonaktifan akses. Terima kasih!";

            $response = $fonnteService->sendMessage($user->nomor_wa, $message);

            if (isset($response['status']) && $response['status']) {
                $this->info("Notifikasi terkirim ke {$user->name} ({$user->nomor_wa})");
            } else {
                $this->error("Gagal mengirim ke {$user->name}: " . ($response['message'] ?? 'Unknown Error'));
            }
        }

        $this->info('Proses pengiriman notifikasi selesai.');
    }
}
