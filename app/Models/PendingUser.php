<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Check and reject expired registration requests (older than 24h).
     */
    public static function checkExpiry()
    {
        self::where('status', 'pending')
            ->where('created_at', '<', now()->subDay())
            ->update(['status' => 'rejected']);
    }
}
