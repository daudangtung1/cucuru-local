<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all()->pluck('id')->toArray();

        for ($i = 0; $i < 100; $i ++) {
            Notification::create([
                'user_id' => array_rand($users),
                'type' => 1,
                'notifiable_type' => 1,
                'notifiable_id' => 1,
                'data' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
                'is_important' => rand(0, 1),
            ]);
        }
    }
}
