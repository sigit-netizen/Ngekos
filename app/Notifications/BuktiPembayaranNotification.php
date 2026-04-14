<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Illuminate\Contracts\Queue\ShouldQueue;

class BuktiPembayaranNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $paymentData;

    /**
     * Create a new notification instance.
     * $paymentData should contain:
     * - type: 'tenant_order', 'owner_reg', 'owner_sub'
     * - name: sender name
     * - target_name: receiver name (optional)
     * - kos_name: (optional)
     * - amount: (optional)
     * - plan_name: (optional)
     */
    public function __construct($paymentData)
    {
        $this->paymentData = $paymentData;
    }

    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $type = $this->paymentData['type'] ?? 'payment';
        $title = 'Bukti Pembayaran Baru!';
        
        if ($type === 'owner_reg') {
            $title = 'Bukti Registrasi Owner';
        } elseif ($type === 'owner_sub') {
            $title = 'Bukti Bayar Langganan';
        }

        return (new WebPushMessage)
            ->title($title)
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Ada unggahan bukti pembayaran baru dari " . ($this->paymentData['name'] ?? 'User') . ".")
            ->action('Cek Sekarang', 'dashboard');
    }

    public function toFonnte($notifiable)
    {
        $type = $this->paymentData['type'] ?? 'payment';
        $name = $this->paymentData['name'] ?? 'User';
        $kos = $this->paymentData['kos_name'] ?? 'Kos';
        $amount = isset($this->paymentData['amount']) ? number_format($this->paymentData['amount'], 0, ',', '.') : '-';
        $plan = $this->paymentData['plan_name'] ?? 'Paket';

        $header = "[NGEKOS.ID - BUKTI PEMBAYARAN]";
        $message = "";

        if ($type === 'tenant_order') {
            $message = "{$header}\n\n" .
                       "Halo, penyewa {$name} telah mengunggah bukti pembayaran!\n" .
                       "---------------------------\n" .
                       "Kos: {$kos}\n" .
                       "Nominal: Rp {$amount}\n" .
                       "---------------------------\n" .
                       "Segera verifikasi di dashboard untuk mengaktifkan pesanan.";
        } elseif ($type === 'owner_reg') {
            $message = "{$header}\n\n" .
                       "Halo Super Admin, calon owner {$name} telah mengunggah bukti pendaftaran!\n" .
                       "---------------------------\n" .
                       "Paket: {$plan}\n" .
                       "---------------------------\n" .
                       "Mohon segera diproses di panel admin.";
        } elseif ($type === 'owner_sub') {
            $message = "{$header}\n\n" .
                       "Halo Super Admin, owner {$name} telah membayar perpanjangan paket!\n" .
                       "---------------------------\n" .
                       "Paket: {$plan}\n" .
                       "---------------------------\n" .
                       "Segera konfirmasi di panel admin.";
        } else {
            $message = "{$header}\n\n" .
                       "Ada bukti pembayaran baru dari {$name}. Mohon segera diperiksa di dashboard.";
        }

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Bukti Pembayaran Baru',
            'message' => 'Bukti pembayaran dari ' . ($this->paymentData['name'] ?? 'User') . ' telah diunggah.',
            'data' => $this->paymentData
        ];
    }
}
