<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Hash;

class PendingUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Budi Penyewa',
                'nik' => '1234567890123456',
                'nomor_wa' => '081234567890',
                'tanggal_lahir' => '2000-01-01',
                'alamat' => 'Jl. Contoh No. 123',
                'id_plans' => 1, // Anak Kos
                'email' => 'budi.penyewa@test.com',
                'password' => Hash::make('password'),
                'plan_type' => null,
                'package_type' => null,
                'jumlah_kamar' => 0,
                'status' => 'pending',
            ],
            [
                'name' => 'Andi Owner Pro',
                'nik' => '1234567890123457',
                'nomor_wa' => '081234567891',
                'tanggal_lahir' => '1990-05-15',
                'alamat' => 'Jl. Owner No. 1',
                'id_plans' => 2, // Pemilik Kos
                'email' => 'andi.pro@test.com',
                'password' => Hash::make('password'),
                'plan_type' => 'pro',
                'package_type' => null,
                'jumlah_kamar' => 10,
                'status' => 'pending',
            ],
            [
                'name' => 'Siti Owner Premium',
                'nik' => '1234567890123458',
                'nomor_wa' => '081234567892',
                'tanggal_lahir' => '1985-11-20',
                'alamat' => 'Jl. Owner No. 2',
                'id_plans' => 2, // Pemilik Kos
                'email' => 'siti.premium@test.com',
                'password' => Hash::make('password'),
                'plan_type' => 'premium',
                'package_type' => 'monthly',
                'jumlah_kamar' => 20,
                'status' => 'pending',
            ],
            [
                'name' => 'Joko Owner Per Kamar Pro',
                'nik' => '1234567890123459',
                'nomor_wa' => '081234567893',
                'tanggal_lahir' => '1988-03-10',
                'alamat' => 'Jl. Owner No. 3',
                'id_plans' => 2, // Pemilik Kos
                'email' => 'joko.perkamar@test.com',
                'password' => Hash::make('password'),
                'plan_type' => 'pro_perkamar',
                'package_type' => null,
                'jumlah_kamar' => 5,
                'status' => 'pending',
            ],
        ];

        foreach ($data as $item) {
            PendingUser::create($item);
        }
    }
}
