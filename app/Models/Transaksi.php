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
        'jatuh_tempo',
    ];

    const TYPE_BOOKING = 'booking';
    const TYPE_SEWA = 'sewa';

    protected $casts = [
        'batas_bayar' => 'datetime',
        'tanggal_pembayaran' => 'datetime',
        'jatuh_tempo' => 'date',
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
     * Dapatkan deskripsi alasan kegagalan untuk transaksi yang gagal/ditolak.
     * Digunakan terutama untuk tampilan desktop.
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
     * Dapatkan deskripsi alasan kegagalan yang lebih pendek.
     * Digunakan terutama untuk tampilan kartu mobile.
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
     * Periksa dan keluarkan (evict) penyewa dengan akun "Mati" (terlambat > 3 hari).
     */
    public static function checkDeadAccounts($kodeKos = null)
    {
        // Cari pengguna yang saat ini menjadi penyewa (memiliki id_kamar)
        $query = User::whereNotNull('id_kamar');
        
        if ($kodeKos) {
            $kos = Kos::where('kode_kos', $kodeKos)->first();
            if ($kos) {
                $query->where('id_kos', $kos->id);
            }
        }

        $tenants = $query->get();

        foreach ($tenants as $tenant) {
            // Dapatkan transaksi sewa berbayar terbaru (latest paid rent transaction)
            $lastRent = self::where('id_user', $tenant->id)
                ->where('status', 'paid')
                ->whereIn('tipe', [self::TYPE_BOOKING, self::TYPE_SEWA])
                ->latest()
                ->first();

            if ($lastRent && $lastRent->jatuh_tempo) {
                $nowWib = now('Asia/Jakarta')->startOfDay();
                $expiryWib = \Carbon\Carbon::parse($lastRent->jatuh_tempo)->timezone('Asia/Jakarta')->startOfDay();
                
                $daysDiff = (int) $nowWib->diffInDays($expiryWib, false);

                // Jika terlambat lebih dari 2 hari (Hari ke-3 adalah Mati/Nonaktif)
                if ($daysDiff < -2) {
                    $tenant->evict();
                }
            }
        }
    }

    /**
     * Periksa dan batalkan transaksi terverifikasi yang kedaluwarsa (expired).
     * Sering dipanggil sebelum menampilkan daftar pesanan (order) untuk memastikan status terbaru.
     */
    public static function checkExpiry()
    {
        // 1. Menangani pesanan Terverifikasi (Menunggu Pembayaran) - batas 24 jam (batas_bayar)
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

        // 2. Menangani pesanan Tertunda (Menunggu Verifikasi Admin) - batas 24 jam (limit)
        self::where('status', 'pending')
            ->where('created_at', '<', now()->subDay())
            ->update(['status' => 'rejected']);

        // 3. Menangani pesanan Terverifikasi dengan Bukti (Menunggu Konfirmasi Admin) - batas 24 jam (limit)
        $expiredUnconfirmed = self::where('status', 'verified')
            ->whereNotNull('bukti_pembayaran')
            ->where('updated_at', '<', now()->subDay())
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
