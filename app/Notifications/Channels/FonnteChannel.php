<?php
 
namespace App\Notifications\Channels;
 
use Illuminate\Notifications\Notification;
use App\Services\FonnteService;
 
class FonnteChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        \Log::info('FonnteChannel: Attempting to send notification to ' . ($notifiable->email ?? 'unknown'));
        
        if (!method_exists($notification, 'toFonnte')) {
            \Log::warning('FonnteChannel: toFonnte method missing in notification ' . get_class($notification));
            return;
        }

        $data = $notification->toFonnte($notifiable);
        \Log::info('FonnteChannel: Data returned from toFonnte: ', $data);
        
        $target = $data['target'] ?? ($notifiable->nomor_wa ?: $notifiable->email);
        $message = $data['message'] ?? null;

        if (empty($target) || empty($message)) {
            \Log::warning('FonnteChannel: Missing target or message. Target: ' . ($target ?? 'empty') . ', Message length: ' . (isset($message) ? strlen($message) : 'empty'));
            return;
        }

        $fonnte = new FonnteService();
        $response = $fonnte->sendMessage($target, $message);
        
        if (isset($response['status']) && $response['status'] == false) {
            \Log::error('Fonnte WhatsApp Error: ' . ($response['message'] ?? 'Unknown error') . ' to ' . $target);
        } elseif (!$response) {
            \Log::error('Fonnte WhatsApp Error: Empty response from API for ' . $target);
        }
    }
}
