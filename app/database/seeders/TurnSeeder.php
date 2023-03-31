<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Turn;
use Illuminate\Database\Seeder;

class TurnSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Turn::firstOrCreate(
            ['id' =>  1],
        );
    }
}
