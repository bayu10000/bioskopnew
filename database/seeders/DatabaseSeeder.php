<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan: Film harus dulu karena Showtime tergantung Film.
        $this->call([
            // FilmSeeder::class,
            RuanganSeeder::class,
            SeatSeeder::class,
            ShowtimeSeeder::class,
            // Seeder lain...
        ]);
    }
}
