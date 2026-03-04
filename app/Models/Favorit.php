<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    use HasFactory;

    protected $table = 'favorits';

    protected $fillable = [
        'id_user',
        'id_kos',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kos()
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }
}
