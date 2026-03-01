<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kos extends Model
{
    use HasFactory;

    protected $table = 'kos';

    protected $fillable = [
        'kode_kos',
        'nama_kos',
        'alamat',
        'kategori',
        'id_user',
        'is_kode_kos_edited',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'id_kos');
    }
}
