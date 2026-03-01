<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama_fasilitas',
        'id_kamar',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'id_kamar');
    }
}
