<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class OwnerRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ownerData;

    public function __construct($ownerData)
    {
        $this->ownerData = $ownerData;
    }

    public function via(object $notifiable): array
    {
        return ['database', FonnteChannel::class, WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Registrasi Owner Baru!')
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Ada pendaftaran pemilik kos baru: " . ($this->ownerData['nama'] ?? 'User') . ".")
            ->action('Cek Verifikasi', 'dashboard');
    }

    // WebPush removed

    public function toFonnte($notifiable)
    {
        $name = $this->ownerData['name'] ?? 'Owner';
        $email = $this->ownerData['email'] ?? '-';
        $plan = $this->ownerData['plan'] ?? 'Pemilik Kos';

        $message = "[NGEKOS.ID - PENDAFTARAN OWNER]\n\n" .
                   "Halo Super Admin, ada pendaftaran baru!\n" .
                   "---------------------------\n" .
                   "Nama: {$name}\n" .
                   "Email: {$email}\n" .
                   "Jenis: {$plan}\n" .
                   "---------------------------\n" .
                   "Segera verifikasi data calon owner tersebut.";

        return ['message' => $message];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pendaftaran Pemilik Kos Baru',
            'message' => 'Pemilik Kos baru bernama ' . ($this->ownerData['name'] ?? 'Owner') . ' telah mendaftar.',
            'data' => $this->ownerData
        ];
    }
}
