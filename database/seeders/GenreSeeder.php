<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = ['Action', 'Comedy', 'Drama', 'Horror', 'Sci-Fi', 'Fantasy', 'Romance', 'Thriller', 'Animation'];
        foreach ($genres as $genre) {
            Genre::firstOrCreate(['nama' => $genre]);
        }
    }
}
