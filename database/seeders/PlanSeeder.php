<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            Plan::create([
                'name' => "Test Plan $i",
                'monthly_fee' => rand(0, 5),
                'user_id' => rand(1, 10),
                'genre_id' => 1,
                'description' => 0,
                'viewing_restriction' => 0,
                'set_back_number_sale' => 0,
            ]);
        }
    }
}
