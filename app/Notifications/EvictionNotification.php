<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;

class EvictionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $evictionData;

    /**
     * Create a new notification instance.
     * $evictionData should contain:
     * - kos_name
     * - nomor_kamar
     * - owner_phone
     */
    public function __construct($evictionData)
    {
        $this->evictionData = $evictionData;
    }

    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Status Sewa Berakhir')
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Anda telah dikeluarkan dari kamar di " . ($this->evictionData['kos_name'] ?? 'Kos') . ".")
            ->action('Cek Detail', 'dashboard');
    }

    public function toFonnte($notifiable)
    {
        $nama = $notifiable->name ?? 'Penyewa';
        $kos = $this->evictionData['kos_name'] ?? 'Kos';
        $kamar = $this->evictionData['nomor_kamar'] ?? '-';

        $message = "[NGEKOS.ID - PEMBERITAHUAN]\n\n" .
                   "Halo {$nama},\n" .
                   "Kami informasikan bahwa masa sewa Anda di {$kos} (Kamar {$kamar}) telah berahir atau dibatalkan oleh pengelola.\n\n" .
                   "Kamar tersebut kini telah dikosongkan kembali di sistem. Jika ini adalah kekeliruan, silakan hubungi pengelola kos (" . ($this->evictionData['owner_phone'] ?? '-') . ")\n" .
                   "---------------------------\n" .
                   "Terima kasih telah menggunakan layanan kami.";

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Status Sewa Berakhir',
            'message' => 'Anda telah dikeluarkan dari kamar di ' . ($this->evictionData['kos_name'] ?? 'Kos'),
            'data' => $this->evictionData
        ];
    }
}
