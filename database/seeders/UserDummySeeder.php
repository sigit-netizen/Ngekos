<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Langganan;
use App\Models\JenisLangganan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Roles exist
        $roles = ['admin', 'pro', 'premium', 'per_kamar_pro', 'per_kamar_premium', 'users', 'superadmin'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Define Dummy Users
        $dummyUsers = [
            [
                'name' => 'User Anak Kos',
                'email' => 'user@mail.com',
                'id_plans' => 1, // Anak Kos
                'role_spatie' => 'users',
                'langganan_nama' => 'MEMBER BIASA',
                'jumlah_kamar' => 0
            ],
            [
                'name' => 'Admin Pro',
                'email' => 'pro@mail.com',
                'id_plans' => 2, // Pro
                'role_spatie' => 'pro',
                'langganan_nama' => 'MEMBER PRO',
                'jumlah_kamar' => 0
            ],
            [
                'name' => 'Admin Premium',
                'email' => 'premium@mail.com',
                'id_plans' => 3, // Premium
                'role_spatie' => 'premium',
                'langganan_nama' => 'MEMBER PREMIUM',
                'jumlah_kamar' => 0
            ],
            [
                'name' => 'Admin Per Kamar Pro',
                'email' => 'perkamar_pro@mail.com',
                'id_plans' => 5, // Pro Per Kamar
                'role_spatie' => 'per_kamar_pro',
                'langganan_nama' => 'PER KAMAR PRO',
                'jumlah_kamar' => 20
            ],
            [
                'name' => 'Admin Per Kamar Premium',
                'email' => 'perkamar_premium@mail.com',
                'id_plans' => 4, // Premium Per Kamar
                'role_spatie' => 'per_kamar_premium',
                'langganan_nama' => 'PER KAMAR PREMIUM',
                'jumlah_kamar' => 10
            ],
            [
                'name' => 'Superadmin System',
                'email' => 'superadmin@mail.com',
                'id_plans' => 6, // Superadmin
                'role_spatie' => 'superadmin',
                'langganan_nama' => 'MEMBER PRO', // Superadmin usually pro
                'jumlah_kamar' => 0
            ],
        ];

        foreach ($dummyUsers as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'id_plans' => $data['id_plans'],
                    'nik' => rand(1000000000, 9999999999),
                    'nomor_wa' => '08' . rand(100000000, 999999999),
                    'tanggal_lahir' => '1990-01-01',
                    'alamat' => 'Alamat Dummy',
                ]
            );

            // Role assignment logic
            $currentRoles = [$data['role_spatie']];
            
            // Add 'admin' role if it's an owner/admin plan (id_plans 2,3,4,5)
            if (in_array($data['id_plans'], [2, 3, 4, 5])) {
                $currentRoles[] = 'admin';
            }
            
            $user->syncRoles($currentRoles);

            // Create Langganan Record
            $jenis = JenisLangganan::where('nama', $data['langganan_nama'])->first();
            if ($jenis) {
                Langganan::updateOrCreate(
                    ['id_user' => $user->id],
                    [
                        'id_langganan' => $jenis->id,
                        'jumlah_kamar' => $data['jumlah_kamar'],
                        'status' => 'active',
                        'tanggal_pembayaran' => now(),
                    ]
                );
            }
        }
    }
}
