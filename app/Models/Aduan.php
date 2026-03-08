<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aduan extends Model
{
    protected $table = 'aduans';

    protected $fillable = [
        'id_user',
        'id_kos',
        'judul',
        'pesan',
        'kategori', // fasilitas | tambah | lainnya
        'status',
        'dibaca_at',
    ];

    protected $casts = [
        'dibaca_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kos(): BelongsTo
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }

    public function isRead(): bool
    {
        return $this->status === 'sudah_dibaca';
    }
}
