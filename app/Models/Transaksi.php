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
        'tipe',
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

    const TYPE_BOOKING = 'booking';
    const TYPE_SEWA = 'sewa';

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
     * Get the failure reason description for failed/rejected transactions.
     * Used mainly for desktop views.
     */
    public function getKeteranganGagalAttribute()
    {
        if ($this->status === 'rejected') {
            return $this->bukti_pembayaran
                ? 'Ditolak Admin (Bukti Tidak Valid)'
                : 'Ditolak Admin (Saat Pengajuan)';
        } elseif ($this->status === 'failed') {
            return $this->bukti_pembayaran
                ? 'Batal Otomatis (Melewati Waktu Konfirmasi)'
                : 'Batal Otomatis (Melewati Batas Bayar)';
        }
        return '-';
    }

    /**
     * Get the shorter failure reason description.
     * Used mainly for mobile card views.
     */
    public function getKeteranganGagalSingkatAttribute()
    {
        if ($this->status === 'rejected') {
            return $this->bukti_pembayaran
                ? 'Ditolak Admin (Bukti)'
                : 'Ditolak Admin (Pengajuan)';
        } elseif ($this->status === 'failed') {
            return $this->bukti_pembayaran
                ? 'Batal Waktu Konfirmasi'
                : 'Batal Batas Bayar';
        }
        return '-';
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
