<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class AduanFasilitasNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $aduanData;

    public function __construct($aduanData)
    {
        $this->aduanData = $aduanData;
    }

    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $kategori = $this->aduanData['kategori'] ?? 'Aduan';
        $title = ($kategori === 'tambah') ? 'Permintaan Fasilitas Baru' : 'Laporan Aduan Fasilitas';

        return (new WebPushMessage)
            ->title($title)
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Dari " . ($this->aduanData['nama'] ?? 'User') . " di Kos " . ($this->aduanData['kos'] ?? 'Anda') . ".")
            ->action('Cek Detail', 'dashboard');
    }

    // WebPush removed

    public function toFonnte($notifiable)
    {
        $nama = $this->aduanData['nama'] ?? 'Anak Kos';
        $kos = $this->aduanData['kos'] ?? 'Kos Anda';
        $judul = $this->aduanData['judul'] ?? '-';
        $pesan = $this->aduanData['pesan'] ?? '-';
        $kategori = $this->aduanData['kategori'] ?? 'fasilitas';

        $tag = strtoupper($kategori);
        
        $message = "[NGEKOS.ID - AJUAN {$tag}]\n\n" .
                   "Halo, ada ajuan baru dari {$nama}!\n" .
                   "---------------------------\n" .
                   "Kos: {$kos}\n" .
                   "Subjek: {$judul}\n" .
                   "Pesan: {$pesan}\n" .
                   "---------------------------\n" .
                   "Mohon segera diperiksa di dashboard.";

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Ajuan Fasilitas Baru',
            'message' => 'Ada ajuan/aduan fasilitas baru dari ' . ($this->aduanData['nama'] ?? 'Anak Kos'),
            'data' => $this->aduanData
        ];
    }
}
