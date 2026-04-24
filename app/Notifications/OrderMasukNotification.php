<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class OrderMasukNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $orderData;

    /**
     * Buat instance notifikasi baru (Create a new notification instance).
     */
    public function __construct($orderData = null)
    {
        $this->orderData = $orderData;
    }

    /**
     * Dapatkan saluran pengiriman (delivery channels) notifikasi.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Order Masuk Baru!')
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Ada pesanan baru dari " . ($this->orderData['nama'] ?? 'User') . " di " . ($this->orderData['kos'] ?? 'Kos') . ".")
            ->action('Cek Dashboard', 'dashboard');
    }

    // WebPush dihapus

    public function toFonnte($notifiable)
    {
        $nama = $this->orderData['nama'] ?? 'User';
        $kos = $this->orderData['kos'] ?? 'Kos Anda';
        $kamar = $this->orderData['kamar'] ?? '-';
        $jumlah = number_format($this->orderData['jumlah'] ?? 0, 0, ',', '.');
        $tipe = $this->orderData['tipe'] ?? 'order';
        
        $tag = ($tipe === 'sewa') ? 'PEMBAYARAN SEWA' : 'BOOKING BARU';
        
        // Pesan dinamis berdasarkan siapa yang diberi tahu (notified)
        $ownerName = $this->orderData['owner_name'] ?? 'Pihak Kos';
        
        $message = "[NGEKOS.ID - NOTIFIKASI {$tag}]\n\n" .
                   "Halo, ada {$tag} masuk!\n" .
                   "---------------------------\n" .
                   "Penyewa: {$nama}\n" .
                   "Kos: {$kos}\n" .
                   "Kamar: {$kamar}\n" .
                   "Nominal: Rp {$jumlah}\n";
        
        if (isset($this->orderData['is_superadmin']) && $this->orderData['is_superadmin']) {
            $message .= "Pemilik Kos: {$ownerName}\n";
        }
        
        $message .= "---------------------------\n" .
                    "Segera login ke dashboard untuk verifikasi.";

        return [
            'message' => $message
        ];
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order Kos Baru!',
            'message' => $this->orderData ? 'Order baru dari ' . ($this->orderData['nama'] ?? 'User') . ' masuk.' : 'Ada order baru masuk, segera periksa!',
            'data' => $this->orderData
        ];
    }
}
