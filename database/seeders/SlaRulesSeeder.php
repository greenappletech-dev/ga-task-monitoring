<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SlaRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sla_rules')->insert([
            ['priority' => 'Urgent', 'business_days' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'High', 'business_days' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'Medium', 'business_days' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'Low', 'business_days' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
