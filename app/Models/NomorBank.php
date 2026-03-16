<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomorBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik',
        'nama_bank_2',
        'nomor_rekening_2',
        'nama_pemilik_2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
