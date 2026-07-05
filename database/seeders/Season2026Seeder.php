<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Season2026Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * docker compose exec app php artisan db:seed --class=Season2026Seeder
     */
    public function run(): void
    {
        // schedules	CREATE TABLE `schedules` (`year`, `week`, `home_id`, `away_id`, `game_time_id`, `game_type_id`, `order`)
        for ($i = 1; $i <= 10; $i++) {
            Schedule::create([
                'year'         => $i,
                'week'         => $i,
                'home_id'      => $i,
                'away_id'      => $i,
                'game_time_id' => $i,
                'game_type_id' => $i,
                'order'        => $i,
            ]);
        }
    }
}
