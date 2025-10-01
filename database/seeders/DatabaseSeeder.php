<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GenreSeeder::class,
            RuanganSeeder::class,
            // FilmSeeder::class,     // pastikan FilmSeeder diaktifkan agar film ter-seed
            SeatSeeder::class,     // buat template kursi per ruangan
            ShowtimeSeeder::class, // buat showtime + kursi per showtime
        ]);
    }
}
