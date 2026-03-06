<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamar';

    protected $fillable = [
        'nomor_kamar',
        'status',
        'harga',
        'foto',
        'id_kos',
        'durasi_sewa',
        'tipe_durasi',
    ];

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_kamar');
    }

    public function fasilitas()
    {
        return $this->hasMany(Fasilitas::class, 'id_kamar');
    }
}
