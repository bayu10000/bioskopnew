<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            // Tambahkan seeder yang Anda butuhkan di sini
            // UserSeeder::class,
            // GenreSeeder::class,
            // RuanganSeeder::class,
            // FilmSeeder::class,
            ShowtimeSeeder::class,
        ]);
    }
}
