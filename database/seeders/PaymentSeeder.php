<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentCard;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cards = PaymentCard::all()->toArray();

        for ($i = 0; $i < 100; $i++) {
            $cardIndex = array_rand($cards);
            $originAmount = rand(20, 100);
            $date = Carbon::today()->subDays(rand(0, 10));
            Payment::insert([
                'amount' => $originAmount - rand(0, 10),
                'origin_amount' => $originAmount,
                'status' => rand(0,2),
                'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                'payment_card_id' => $cards[$cardIndex]['id'],
                'created_by' => $cards[$cardIndex]['created_by'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
