<?php

namespace Database\Seeders;

use App\Models\PaymentCard;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all()->toArray();

        for ($i = 1; $i <= 10; $i++) {
            PaymentCard::create([
                'card_name' => strtoupper(Str::random(10)),
                'card_number' => mt_rand(100000,999999),
                'card_type' => rand(1,3),
                'bank_name' => 'Bank name',
                'expired_date' => '01/01/2030',
                'created_by' => $users[array_rand($users)]['id'],
            ]);
        }
    }
}
