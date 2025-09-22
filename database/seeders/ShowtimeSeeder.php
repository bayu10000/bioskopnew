<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use App\Models\Ruangan;
use App\Models\Showtime;
use Illuminate\Support\Carbon;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $films = Film::all();
        $ruangans = Ruangan::all();

        if ($films->isEmpty() || $ruangans->isEmpty()) {
            $this->command->info('Tidak ada film atau ruangan. Jalankan FilmSeeder dan RuanganSeeder terlebih dahulu.');
            return;
        }

        $numberOfShowtimes = 50; // total showtime yang ingin dibuat
        $jamSlots = ['10:00', '13:00', '16:00', '19:00', '21:00'];

        $created = 0;
        for ($i = 0; $i < $numberOfShowtimes; $i++) {
            $film = $films->random();
            $ruangan = $ruangans->random();

            // Pilih tanggal acak dalam 1-14 hari ke depan (tidak mengubah objek yang sama)
            $tanggal = Carbon::now()->addDays(rand(1, 14))->toDateString();

            // Pilih jam secara acak
            $jam = $jamSlots[array_rand($jamSlots)];

            $harga = rand(30000, 50000);

            // Cegah duplikat simple: sama film, ruangan, tanggal, jam
            $exists = Showtime::where('film_id', $film->id)
                ->where('ruangan_id', $ruangan->id)
                ->where('tanggal', $tanggal)
                ->where('jam', $jam)
                ->exists();

            if ($exists) {
                continue;
            }

            Showtime::create([
                'film_id' => $film->id,
                'ruangan_id' => $ruangan->id,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'harga' => $harga,
            ]);

            $created++;
        }

        $this->command->info("Showtime seeded: {$created} entries created.");
    }
}
