<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasKos extends Model
{
    use HasFactory;

    protected $table = 'fasilitas_kos';

    protected $fillable = [
        'id_kos',
        'nama_fasilitas',
        'harga_tambahan',
    ];

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }
}
