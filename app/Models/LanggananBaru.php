<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanggananBaru extends Model
{
    use HasFactory;

    protected $table = 'langganan_barus';

    protected $fillable = [
        'id_user',
        'id_langganan',
        'status',
        'jumlah_kamar',
        'bukti_pembayaran',
        'metode_pembayaran'
    ];

    public function jenis_langganan()
    {
        return $this->belongsTo(JenisLangganan::class, 'id_langganan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
