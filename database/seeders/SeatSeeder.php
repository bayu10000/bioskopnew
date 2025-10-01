<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $showtimes = Showtime::with('ruangan')->get();

        if ($showtimes->isEmpty()) {
            $this->command->info('❌ Tidak ada showtime. Buat showtime dulu.');
            return;
        }

        // Hapus kursi lama (pakai delete, bukan truncate biar aman dari FK order_details)
        Seat::query()->delete();

        $created = 0;

        foreach ($showtimes as $showtime) {
            $kapasitas = 100; // fix 100 kursi per ruangan
            $rows = range('A', 'J'); // 10 baris (A–J)
            $cols = 10; // 10 kolom per baris

            $count = 0;
            foreach ($rows as $row) {
                for ($col = 1; $col <= $cols; $col++) {
                    if ($count >= $kapasitas) break 2;

                    Seat::create([
                        'showtime_id' => $showtime->id,
                        'nomor_kursi' => $row . $col,
                        'status'      => 'available',
                    ]);

                    $count++;
                    $created++;
                }
            }

            $this->command->info("✔ Kursi untuk Showtime ID {$showtime->id} (Ruangan {$showtime->ruangan->nama}) berhasil dibuat: {$count}");
        }

        $this->command->info("🎉 Total kursi dibuat: {$created}");
    }
}
