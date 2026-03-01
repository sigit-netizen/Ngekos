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
}
