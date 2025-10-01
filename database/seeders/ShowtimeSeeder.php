<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use App\Models\Ruangan;
use App\Models\Showtime;
use App\Models\Seat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        $films = Film::all();
        $ruangans = Ruangan::all();

        if ($films->isEmpty() || $ruangans->isEmpty()) {
            $this->command->info('Tidak ada film atau ruangan. Jalankan seeder Film dan Ruangan terlebih dahulu.');
            return;
        }

        // Hapus kursi & showtime lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Seat::whereNotNull('showtime_id')->delete();
        Showtime::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $jams = ['10:00', '16:00', '19:00', '22:00']; // HARUS sama dengan dropdown
        $createdShowtimes = 0;
        $createdSeats = 0;

        foreach ($films as $film) {
            foreach ($ruangans as $ruangan) {
                if (!$film->tanggal_mulai || !$film->tanggal_selesai) {
                    continue;
                }

                $startDate = Carbon::parse($film->tanggal_mulai)->startOfDay();
                $endDate = Carbon::parse($film->tanggal_selesai)->endOfDay();

                // Loop tanggal dari mulai sampai selesai
                for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                    foreach ($jams as $jam) {
                        $showtime = Showtime::firstOrCreate(
                            [
                                'film_id' => $film->id,
                                'ruangan_id' => $ruangan->id,
                                'tanggal' => $date->toDateString(),
                                'jam' => $jam,
                            ],
                            [
                                'harga' => 30000,
                            ]
                        );

                        if ($showtime->wasRecentlyCreated) {
                            $createdShowtimes++;

                            // Generate seats sesuai kapasitas ruangan
                            $kapasitas = (int) ($ruangan->kapasitas ?? 100);
                            if ($kapasitas <= 0) {
                                continue;
                            }

                            $allRows = range('A', 'Z');
                            $rowCount = min(count($allRows), (int) ceil($kapasitas / 10));
                            $rows = array_slice($allRows, 0, $rowCount);
                            $colCount = (int) ceil($kapasitas / count($rows));

                            for ($j = 1; $j <= $kapasitas; $j++) {
                                $row = $rows[floor(($j - 1) / $colCount)];
                                $col = ($j - 1) % $colCount + 1;
                                $nomor_kursi = $row . $col;

                                Seat::create([
                                    'showtime_id' => $showtime->id,
                                    'nomor_kursi' => $nomor_kursi,
                                    'status' => 'available',
                                    'harga' => 30000,
                                ]);
                                $createdSeats++;
                            }
                        }
                    }
                }
            }
        }

        $this->command->info("Showtimes seeded: {$createdShowtimes}. Showtime seats seeded: {$createdSeats} (harga Rp 30.000).");
    }
}
