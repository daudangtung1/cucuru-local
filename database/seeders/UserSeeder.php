<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'account_type' => 0,
                'is_active' => rand(0, 1),
                'email' => "test{$i}@admin.com",
                'user_name' => "user_name_{$i}",
                'full_name' => "full_name_{$i}",
                'password' => Hash::make('123456'),
                'email_verified_at' => \Carbon\Carbon::now()
            ]);
        }
    }
}
