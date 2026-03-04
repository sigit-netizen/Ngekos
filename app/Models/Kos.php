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
        'no_rekening',
        'kategori',
        'id_user',
        'is_kode_kos_edited',
        'foto',
        'kota',
        'nama_kota',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($kos) {
            if ($kos->isDirty('kota') && !$kos->isDirty('nama_kota')) {
                $kos->nama_kota = $kos->kota;
            } elseif ($kos->isDirty('nama_kota') && !$kos->isDirty('kota')) {
                $kos->kota = $kos->nama_kota;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'id_kos');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorits', 'id_kos', 'id_user')->withTimestamps();
    }
}
