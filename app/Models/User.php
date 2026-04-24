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
     * Atribut yang dapat diisi secara massal (mass assignable).
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
     * Atribut yang harus disembunyikan untuk serialisasi (serialization).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Dapatkan nama paket berdasarkan pemetaan id_plans
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
     * Periksa apakah pengguna adalah penyewa (tenant) yang terverifikasi.
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
     * Dapatkan tanggal mulai sewa pengguna untuk kamar saat ini.
     * Dihitung berdasarkan transaksi 'berbayar' (paid) tertua untuk id_kamar saat ini.
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
     * Pulihkan peran pengguna (user roles) berdasarkan ID paket mereka saat ini.
     */
    public function syncPlanRole()
    {
        // Peran yang terkait dengan paket yang harus ditukar (swapped)
        $planRoles = ['pro', 'premium', 'per_kamar_premium', 'per_kamar_pro', 'nonaktif'];

        // Tentukan peran target (target roles)
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

        // Hapus peran paket lama yang TIDAK ada dalam daftar rolesToKeep
        foreach ($planRoles as $role) {
            if (!in_array($role, $rolesToKeep) && $this->hasRole($role)) {
                $this->removeRole($role);
            }
        }

        // Tambahkan peran target yang belum ada
        foreach ($rolesToKeep as $role) {
            if (!$this->hasRole($role)) {
                $this->assignRole($role);
            }
        }

        // PENTING (CRITICAL): Bersihkan cache izin sehingga perubahan segera terlihat di sidebar
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Setel status pengguna menjadi 'aktif' di tabel status_users dan pulihkan peran.
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
     * Setel status pengguna menjadi 'inactive' di tabel status_users dan berikan peran 'nonaktif'.
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
     * Keluarkan penyewa (evict): hapus asosiasi kamar/kos dan lepaskan kamar tersebut.
     */
    public function evict()
    {
        \DB::beginTransaction();
        try {
            // 1. Cari transaksi APAPUN yang mungkin "mengunci" pengguna atau kamar ini (status pending, verified, paid)
            $activeTransactions = \App\Models\Transaksi::where('id_user', $this->id)
                ->whereIn('status', ['pending', 'verified', 'paid'])
                ->get();

            // 2. Lepaskan kamar mana pun yang terkait dengan transaksi ini
            foreach ($activeTransactions as $tx) {
                if ($tx->kamar) {
                    $tx->kamar->update(['status' => 'tersedia']);
                }
            }

            // 3. Tandai transaksi tersebut sebagai 'expired' (kedaluwarsa)
            \App\Models\Transaksi::where('id_user', $this->id)
                ->whereIn('status', ['pending', 'verified', 'paid'])
                ->update(['status' => 'expired']);

            // 4. Paksa pelepasan asosiasi kamar milik pengguna untuk jaga-jaga (just in case)
            $evictionData = null;
            if ($this->kamar) {
                $evictionData = [
                    'kos_name' => $this->kamar->kos->nama_kos ?? ($this->kosAnak->nama_kos ?? 'Kos'),
                    'nomor_kamar' => $this->kamar->nomor_kamar ?? '-',
                    'owner_phone' => $this->kamar->kos->user->nomor_wa ?? null,
                ];
                $this->kamar->update(['status' => 'tersedia']);
            }

            // 5. Hapus asosiasi kamar dan kos milik pengguna
            $this->update([
                'id_kamar' => null,
                'id_kos' => null,
                'status' => 'active', // Kembali ke pengguna aktif umum (general active user)
            ]);

            // Beri tahu Penyewa (Notify Tenant)
            if ($evictionData && $this->nomor_wa) {
                $this->notify(new \App\Notifications\EvictionNotification($evictionData));
            }

            // 6. Reset Peran: Hapus 'users' (penyewa) dan berikan 'user' (umum/general)
            $this->syncRoles(['user']);

            // 7. Pastikan izin (permissions) disinkronkan sesuai dengan Gambar 2 (User Umum)
            $userUmumPermissions = [
                'menu.dashboard',
                'menu.order',
                'menu.profil',
                'fitur.belum_sewa'
            ];
            
            // Sinkronkan izin secara eksplisit untuk pengguna khusus ini agar cocok dengan status 'User Umum'. 
            // Ini akan menghapus 'menu.aduan' dan 'menu.jatuh_tempo' secara otomatis.
            $this->syncPermissions($userUmumPermissions);

            // Bersihkan cache izin Spatie (Spatie permission cache)
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
