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
        $user = User::get()[0];
        $plan = Plan::where('id', 1)->first();

        if ($user && !$plan) {
            Plan::create([
                'name' => 'Test Plan',
                'monthly_fee' => '1000',
                'created_by' => $user->id,
                'description' => 0,
                'genre_id' => 1,
                'viewing_restriction' => 0,
                'set_back_number_sale' => 0
            ]);
        }
    }
}
