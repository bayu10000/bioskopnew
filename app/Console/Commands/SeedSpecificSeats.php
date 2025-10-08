<?php

namespace App\Console\Commands;

use App\Models\Seat;
use App\Models\Showtime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSpecificSeats extends Command
{
    /**
     * Nama dan signature dari console command.
     * Kita menggunakan {showtime_id?} untuk argumen opsional.
     * ? berarti opsional (boleh dikosongkan).
     *
     * @var string
     */
    protected $signature = 'db:seed-seats {showtime_id?}'; // 💡 UBAH DI SINI

    /**
     * Deskripsi console command.
     *
     * @var string
     */
    protected $description = 'Seed seats for all showtimes or a specific showtime ID.';

    /**
     * Jalankan console command.
     *
     * @return int
     */
    public function handle()
    {
        // 1. Ambil ID Showtime dari ARGUMEN command
        $showtimeId = $this->argument('showtime_id');

        $query = Showtime::with('ruangan');

        if ($showtimeId) {
            // Jika ID diberikan, filter query hanya untuk ID tersebut
            $query->where('id', $showtimeId);
            $this->info("Seeding hanya untuk Showtime ID spesifik: {$showtimeId}");
        } else {
            // Jika tidak ada ID, ambil semua showtime
            $this->warn('❌ ID showtime tidak diberikan. Seeding untuk SEMUA showtime yang ada.');
        }

        $showtimes = $query->get();

        if ($showtimes->isEmpty()) {
            $this->error('❌ Tidak ada showtime yang ditemukan.');
            return 1; // Exit code gagal
        }

        $created = 0;

        foreach ($showtimes as $showtime) {
            // 2. Hapus kursi LAMA HANYA untuk showtime ini
            $deletedCount = Seat::where('showtime_id', $showtime->id)->delete();

            $kapasitas = 100;
            $rows = range('A', 'J');
            $cols = 10;
            $seatsToInsert = [];

            $count = 0;
            foreach ($rows as $row) {
                for ($col = 1; $col <= $cols; $col++) {
                    if ($count >= $kapasitas) break 2;

                    $seatsToInsert[] = [
                        'showtime_id' => $showtime->id,
                        'nomor_kursi' => $row . $col,
                        'status'      => 'available',
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];

                    $count++;
                }
            }

            Seat::insert($seatsToInsert);
            $created += $count;
            $this->info("✔ Kursi untuk Showtime ID {$showtime->id} (Ruangan {$showtime->ruangan->nama}) berhasil dibuat: {$count} (Menghapus {$deletedCount} kursi lama).");
        }

        $this->info("🎉 Total kursi dibuat: {$created}");

        return 0; // Exit code sukses
    }
}
