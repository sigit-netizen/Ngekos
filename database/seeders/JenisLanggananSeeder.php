<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisLangganan;

class JenisLanggananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'MEMBER BIASA', 'harga' => 0],
            ['nama' => 'MEMBER PREMIUM', 'harga' => 50000],
            ['nama' => 'MEMBER PRO', 'harga' => 80000],
            ['nama' => 'PER KAMAR PREMIUM', 'harga' => 3000],
            ['nama' => 'PER KAMAR PRO', 'harga' => 5000],
        ];

        foreach ($data as $item) {
            JenisLangganan::updateOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
