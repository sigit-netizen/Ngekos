<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\FonnteChannel;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

use Illuminate\Contracts\Queue\ShouldQueue;

class OrderVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $orderData;

    /**
     * Buat instance notifikasi baru (Create a new notification instance).
     * $orderData harus berisi: 'status', 'nama_kos', 'nomor_kamar', 'kategori' (booking/sewa/registrasi/payment)
     */
    public function __construct($orderData)
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
        $status = $this->orderData['status'] ?? 'verified';
        $title = ($status === 'paid') ? 'Pembayaran Dikonfirmasi!' : (($status === 'rejected') ? 'Order Ditolak' : 'Order Diterima!');

        return (new WebPushMessage)
            ->title($title)
            ->icon('/storage/logo/logo-icon.svg')
            ->body("Status pesanan Anda di " . ($this->orderData['nama_kos'] ?? 'Kos') . " telah diperbarui.")
            ->action('Cek Status', 'dashboard');
    }

    public function toFonnte($notifiable)
    {
        $status = $this->orderData['status'] ?? '';
        $kos = $this->orderData['nama_kos'] ?? 'Kos';
        $kamar = $this->orderData['nomor_kamar'] ?? '-';
        $kategori = $this->orderData['kategori'] ?? 'order';
        $nama = $notifiable->name ?? 'Penyewa';

        $message = "[NGEKOS.ID - STATUS ORDER]\n\n";

        if ($status === 'verified') {
            // Admin menerima pesanan, menunggu pembayaran atau langkah selanjutnya
            $message .= "Halo {$nama}, Order Anda telah DITERIMA!\n" .
                       "---------------------------\n" .
                       "Kos: {$kos}\n" .
                       "Kamar: {$kamar}\n" .
                       "---------------------------\n" .
                       "Segera lakukan pembayaran dan unggah bukti pembayaran di aplikasi agar pesanan Anda diproses sepenuhnya.";
        } elseif ($status === 'paid') {
            // Pembayaran dikonfirmasi (Payment confirmed)
            $message .= "Halo {$nama}, Pembayaran Anda telah DIKONFIRMASI!\n" .
                       "---------------------------\n" .
                       "Kos: {$kos}\n" .
                       "Kamar: {$kamar}\n" .
                       "---------------------------\n" .
                       "Selamat! Akun Anda kini AKTIF. Anda sudah bisa menempati kamar sesuai jadwal. Terima kasih telah menggunakan Ngekos.id!";
        } elseif ($status === 'rejected') {
            $message .= "Halo {$nama}, Mohon maaf, Order Anda DITOLAK.\n" .
                       "---------------------------\n" .
                       "Kos: {$kos}\n" .
                       "Kamar: {$kamar}\n" .
                       "---------------------------\n" .
                       "Silakan hubungi pengelola kos untuk informasi lebih lanjut.";
        } elseif ($kategori === 'registrasi') {
            $message .= "Halo {$nama}, Akun Anda telah DIVERIFIKASI!\n" .
                       "---------------------------\n" .
                       "Kos: {$kos}\n" .
                       "---------------------------\n" .
                       "Pendaftaran Anda sebagai calon penyewa telah disetujui. Silakan login ke aplikasi untuk melihat detail selanjutnya.";
        } else {
            $message .= "Halo {$nama}, ada pembaruan status pada order Anda di {$kos}.";
        }

        return ['message' => $message];
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->orderData['status'] ?? 'verified',
            'title' => 'Pembaruan Status Order',
            'message' => 'Status order Anda di ' . ($this->orderData['nama_kos'] ?? 'Kos') . ' telah diperbarui.',
            'data' => $this->orderData
        ];
    }
}
