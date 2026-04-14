<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nik',
        'nomor_wa',
        'tanggal_lahir',
        'alamat',
        'id_plans',
        'id_kos',
        'id_kamar',
        'status',
        'instagram',
        'twitter',
        'youtube',
        'tiktok',
        'otp',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Get plan name based on id_plans mapping
     */
    public function getPlanName()
    {
        $plans = [
            1 => 'Anak Kos',
            2 => 'Pro',
            3 => 'Premium',
            4 => 'Premium Per Kamar',
            5 => 'Pro Per Kamar',
            6 => 'Superadmin',
        ];

        return $plans[$this->id_plans] ?? 'None';
    }

    public function isPremium()
    {
        return in_array($this->id_plans, [3, 4]);
    }

    public function isPro()
    {
        return in_array($this->id_plans, [2, 5]);
    }

    public function kos()
    {
        return $this->hasMany(Kos::class, 'id_user');
    }

    public function kosAnak()
    {
        return $this->belongsTo(Kos::class, 'id_kos');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'id_kamar');
    }

    /**
     * Check if user is a verified tenant (penyewa).
     */
    public function isPenyewa()
    {
        return !is_null($this->id_kos) && !is_null($this->id_kamar);
    }

    public function langganans()
    {
        return $this->hasMany(Langganan::class, 'id_user');
    }

    public function statusUser()
    {
        return $this->hasOne(StatusUser::class, 'id_user');
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }

    /**
     * Get the date when the user started renting the current room.
     * Calculated based on the oldest 'paid' transaction for the current id_kamar.
     */
    public function getMulaiSewaAttribute()
    {
        if (!$this->id_kamar) {
            return $this->created_at;
        }

        $firstPayment = $this->transaksis()
            ->where('status', 'paid')
            ->where('id_kamar', $this->id_kamar)
            ->oldest('tanggal_pembayaran')
            ->first();

        return $firstPayment ? $firstPayment->tanggal_pembayaran : $this->created_at;
    }

    /**
     * Restore user roles based on their current plan ID.
     */
    public function syncPlanRole()
    {
        // Roles associated with plans that should be swapped
        $planRoles = ['pro', 'premium', 'per_kamar_premium', 'per_kamar_pro', 'nonaktif'];

        // Determine target roles
        $rolesToKeep = ['admin'];

        $roleMap = [
            2 => 'pro',
            3 => 'premium',
            4 => 'per_kamar_premium',
            5 => 'per_kamar_pro'
        ];

        if (isset($roleMap[$this->id_plans])) {
            $rolesToKeep[] = $roleMap[$this->id_plans];
        }

        // Remove old plan roles that are NOT in the rolesToKeep list
        foreach ($planRoles as $role) {
            if (!in_array($role, $rolesToKeep) && $this->hasRole($role)) {
                $this->removeRole($role);
            }
        }

        // Add missing target roles
        foreach ($rolesToKeep as $role) {
            if (!$this->hasRole($role)) {
                $this->assignRole($role);
            }
        }

        // CRITICAL: Clear permission cache so changes reflect in sidebar immediately
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Set user status to 'aktif' in status_users table and restore roles.
     */
    public function activateStatus()
    {
        \App\Models\StatusUser::updateOrCreate(
            ['id_user' => $this->id],
            ['status' => 'aktif']
        );

        $this->syncPlanRole();
    }

    /**
     * Set user status to 'inactive' in status_users table and assign 'nonaktif' role.
     */
    public function deactivateStatus()
    {
        \App\Models\StatusUser::updateOrCreate(
            ['id_user' => $this->id],
            ['status' => 'inactive']
        );

        $this->syncRoles(['nonaktif']);
    }

    /**
     * Evict tenant: clear room/kos association and release the room.
     */
    public function evict()
    {
        \DB::beginTransaction();
        try {
            // 1. Find ANY transactions that might be "locking" this user or rooms (pending, verified, paid)
            $activeTransactions = \App\Models\Transaksi::where('id_user', $this->id)
                ->whereIn('status', ['pending', 'verified', 'paid'])
                ->get();

            // 2. Release any rooms associated with these transactions
            foreach ($activeTransactions as $tx) {
                if ($tx->kamar) {
                    $tx->kamar->update(['status' => 'tersedia']);
                }
            }

            // 3. Mark those transactions as 'expired'
            \App\Models\Transaksi::where('id_user', $this->id)
                ->whereIn('status', ['pending', 'verified', 'paid'])
                ->update(['status' => 'expired']);

            // 4. Force release user's own room association just in case
            $evictionData = null;
            if ($this->kamar) {
                $evictionData = [
                    'kos_name' => $this->kamar->kos->nama_kos ?? ($this->kosAnak->nama_kos ?? 'Kos'),
                    'nomor_kamar' => $this->kamar->nomor_kamar ?? '-',
                    'owner_phone' => $this->kamar->kos->user->nomor_wa ?? null,
                ];
                $this->kamar->update(['status' => 'tersedia']);
            }

            // 5. Clear user's room and kos association
            $this->update([
                'id_kamar' => null,
                'id_kos' => null,
                'status' => 'active', // Back to general active user
            ]);

            // Notify Tenant
            if ($evictionData && $this->nomor_wa) {
                $this->notify(new \App\Notifications\EvictionNotification($evictionData));
            }

            // 6. Reset Roles: Remove 'users' (tenant) and assign 'user' (general)
            $this->syncRoles(['user']);

            // 7. Ensure permissions are synced according to Gambar 2 (User Umum)
            $userUmumPermissions = [
                'menu.dashboard',
                'menu.order',
                'menu.profil',
                'fitur.belum_sewa'
            ];
            
            // Explicitly sync permissions for this specific user to match 'User Umum' state
            // This will remove 'menu.aduan' and 'menu.jatuh_tempo' automatically
            $this->syncPermissions($userUmumPermissions);

            // Clear Spatie permission cache
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            \DB::commit();
            return true;
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Eviction failed for user {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    public function favoritKos()
    {
        return $this->belongsToMany(Kos::class, 'favorits', 'id_user', 'id_kos')->withTimestamps();
    }

    public function nomorBank()
    {
        return $this->hasOne(NomorBank::class, 'user_id');
    }
}
