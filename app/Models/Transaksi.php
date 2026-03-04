<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'jumlah_bayar',
        'tanggal_pembayaran',
        'status',
        'durasi_sewa',
        'tipe_durasi',
        'id_user',
        'id_kamar',
        'kode_kos',
        'catatan',
        'metode_pembayaran',
        'batas_bayar',
        'bukti_pembayaran',
    ];

    protected $casts = [
        'batas_bayar' => 'datetime',
        'tanggal_pembayaran' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'id_kamar');
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'kode_kos', 'kode_kos');
    }

    /**
     * Check and cancel expired verified transactions.
     * Often called before listing orders to ensure up-to-date status.
     */
    public static function checkExpiry()
    {
        // 1. Handle Verified orders (Waiting for Payment) - 24h limit (batas_bayar)
        $expiredUnpaid = self::where('status', 'verified')
            ->whereNull('bukti_pembayaran')
            ->where('batas_bayar', '<', now())
            ->get();

        foreach ($expiredUnpaid as $order) {
            \DB::beginTransaction();
            try {
                $order->update(['status' => 'failed']);
                if ($order->kamar) {
                    $order->kamar->update(['status' => 'tersedia']);
                }
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
            }
        }

        // 2. Handle Pending orders (Waiting for Admin Verification) - 24h limit
        self::where('status', 'pending')
            ->where('created_at', '<', now()->subDay())
            ->update(['status' => 'rejected']);

        // 3. Handle Verified orders with Proof (Waiting for Admin Confirmation) - 24h limit
        $expiredUnconfirmed = self::where('status', 'verified')
            ->whereNotNull('bukti_pembayaran')
            ->where('tanggal_pembayaran', '<', now()->subDay())
            ->get();

        foreach ($expiredUnconfirmed as $order) {
            \DB::beginTransaction();
            try {
                $order->update(['status' => 'failed']);
                if ($order->kamar) {
                    $order->kamar->update(['status' => 'tersedia']);
                }
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
            }
        }
    }
}
