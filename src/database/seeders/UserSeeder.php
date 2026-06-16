<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        User::create([
            'name' => '山田 太郎',
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password'=> Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => '西 怜奈',
            'email' => 'test2@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
       
}
