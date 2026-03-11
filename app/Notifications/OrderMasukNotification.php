<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class OrderMasukNotification extends Notification
{
    use Queueable;

    public $orderData;

    /**
     * Create a new notification instance.
     */
    public function __construct($orderData = null)
    {
        $this->orderData = $orderData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $messageObj = new WebPushMessage();

        $messageObj->title('Order Kos Baru!')
            ->icon('/storage/logo/logo-icon.svg')
            ->body($this->orderData ? 'Order baru dari ' . ($this->orderData['nama'] ?? 'User') . ' masuk.' : 'Ada order baru masuk, segera periksa!')
            ->action('Lihat Order', 'view_order')
            ->data(['url' => route('admin.order')]);

        return $messageObj;
    }

    /**
     * Get the array representation of the notification.
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
