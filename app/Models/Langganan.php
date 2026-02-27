<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langganan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_langganan',
        'tanggal_pembayaran',
        'status',
        'jumlah_kamar'
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
