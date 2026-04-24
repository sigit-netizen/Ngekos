<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class RentDueReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reminderData;

    public function __construct($reminderData)
    {
        $this->reminderData = $reminderData;
    }

    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $prefix = $this->reminderData['prefix'] ?? '';
        $timeText = ($prefix === 'H-3 ') ? "dalam 3 hari" : "besok";

        return (new WebPushMessage)
            ->title('Pengingat Jatuh Tempo')
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Masa sewa Anda di " . ($this->reminderData['kos'] ?? 'Kos') . " akan berakhir {$timeText}.")
            ->action('Bayar Sekarang', 'dashboard');
    }

    // WebPush dihapus

    public function toFonnte($notifiable)
    {
        $nama = $this->reminderData['nama'] ?? 'Pengguna';
        $kos = $this->reminderData['kos'] ?? 'Kos';
        $kamar = $this->reminderData['kamar'] ?? '-';
        $jatuhTempo = $this->reminderData['jatuh_tempo'] ?? '-';

        $prefix = $this->reminderData['prefix'] ?? '';
        $timeText = ($prefix === 'H-3 ') ? "dalam 3 hari" : "besok";

        $message = "[NGEKOS.ID - PENGINGAT SEWA]\n\n" .
                   "Halo {$nama}, jangan lupa ya!\n" .
                   "---------------------------\n" .
                   "Masa sewa Anda di {$kos} (Kamar {$kamar}) akan berakhir {$timeText} pada tanggal {$jatuhTempo}.\n\n" .
                   "Agar tetap bisa nyaman ngekos, yuk segera lakukan pembayaran sewa sebelum jatuh tempo.\n" .
                   "---------------------------\n" .
                   "Abaikan jika Anda sudah membayar.";

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengingat Bayar Sewa',
            'message' => 'Masa sewa kamar Anda akan berakhir besok. Segera lakukan pembayaran.',
            'data' => $this->reminderData
        ];
    }
}
