<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Symfony\Component\Console\Input\InputOption;

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

    // protected function getOptions()
    // {
    //     return [
    //         ['showtime_id', null, InputOption::VALUE_OPTIONAL, 'The specific showtime ID to seed seats for.', null],
    //     ];
    // }
}
