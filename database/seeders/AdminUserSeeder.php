<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@projecttracker.com',
            'email_verified_at' => now(),
            'password' => \Hash::make('password'),
            'roles' => json_encode(['Admin']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
