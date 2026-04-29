<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        User::create([
            'name' => 'テストユーザー1',
            'email' => 'test@test.com',
            'email_verified_at' => now(),
            'password'=> Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'テストユーザー2',
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
