<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class OwnerPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $paymentData;

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
        return (new WebPushMessage)
            ->title('Pembayaran Langganan Owner!')
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Owner " . ($this->paymentData['nama'] ?? 'User') . " telah membayar " . ($this->paymentData['packet'] ?? 'Paket') . ".")
            ->action('Konfirmasi Pembayaran', 'dashboard');
    }

    // WebPush removed

    public function toFonnte($notifiable)
    {
        $owner = $this->paymentData['owner_name'] ?? 'Owner';
        $plan = $this->paymentData['plan_name'] ?? 'Member';
        $jumlah = number_format($this->paymentData['jumlah'] ?? 0, 0, ',', '.');

        $message = "[NGEKOS.ID - PEMBAYARAN MEMBER]\n\n" .
                   "Halo Super Admin, ada setoran masuk!\n" .
                   "---------------------------\n" .
                   "Dari: {$owner}\n" .
                   "Paket: {$plan}\n" .
                   "Nominal: Rp {$jumlah}\n" .
                   "---------------------------\n" .
                   "Segera konfirmasi pembayaran di dashboard.";

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembayaran Sistem Member',
            'message' => 'Konfirmasi pembayaran dari ' . ($this->paymentData['owner_name'] ?? 'Owner'),
            'data' => $this->paymentData
        ];
    }
}
