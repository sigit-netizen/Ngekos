<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
        'status',
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

    public function langganans()
    {
        return $this->hasMany(Langganan::class, 'id_user');
    }

    public function statusUser()
    {
        return $this->hasOne(StatusUser::class, 'id_user');
    }

    /**
     * Restore user roles based on their current plan ID.
     */
    public function syncPlanRole()
    {
        // Remove 'nonaktif' if present
        if ($this->hasRole('nonaktif')) {
            $this->removeRole('nonaktif');
        }

        // Ensure base admin role
        if (!$this->hasRole('admin')) {
            $this->assignRole('admin');
        }

        // Map plan IDs to roles
        $roleMap = [
            2 => 'pro',
            3 => 'premium',
            4 => 'per_kamar_premium',
            5 => 'per_kamar_pro'
        ];

        if (isset($roleMap[$this->id_plans])) {
            $targetRole = $roleMap[$this->id_plans];
            if (!$this->hasRole($targetRole)) {
                $this->assignRole($targetRole);
            }
        }
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
}
