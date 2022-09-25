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
                'email' => "test{$i}@admin.com",
                'username' => "user_name_{$i}",
                'password' => Hash::make('123456'),
                'email_verified_at' => \Carbon\Carbon::now()
            ]);
        }
    }
}
