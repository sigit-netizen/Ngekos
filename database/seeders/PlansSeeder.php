<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['nama_plans' => 'Member'],
            ['nama_plans' => 'Pro'],
            ['nama_plans' => 'Premium'],
            ['nama_plans' => 'Per Kamar Premium'],
            ['nama_plans' => 'Per Kamar Pro'],
            ['nama_plans' => 'Superadmin'],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['nama_plans' => $plan['nama_plans']],
                $plan
            );
        }
    }
}
