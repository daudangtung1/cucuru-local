<?php

namespace Database\Seeders;

use App\Models\FaqType;
use Illuminate\Database\Seeder;

class FaqTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['How to register a account?', 'How to follow a user?', 'How to link bank account?'];

        foreach ($types as $type) {
            FaqType::create([
               'title' => $type
            ]);
        }
    }
}
