<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@ngekos.id'], // Using email as the unique constraint
            [
                'name' => 'John Superadmin',
                'nik' => '9999' . time(),
                'nomor_wa' => '08' . time(),
                'tanggal_lahir' => '1990-01-01',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'id_plans' => 6, // 6 is conventionally the index/plan for superadmin if matched to plans table
            ]
        );

        $superAdmin->assignRole('superadmin');
    }
}
