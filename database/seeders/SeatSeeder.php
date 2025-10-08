<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Pastikan ini di-import

class SeatSeeder extends Seeder
{
    // ❌ Hapus metode getOptions() yang ada di sini!
    // protected function getOptions() { ... }

    public function run(): void
    {
        // 1. Dapatkan ID Showtime dari OPSI/ARGUMEN COMMAND LINE
        // Ini adalah cara paling efektif untuk mengambil opsi kustom dalam konteks db:seed
        $showtimeId = null;

        // Cek argumen CLI (argv) secara manual jika $this->command->option gagal (seperti pada db:seed)
        global $argv;
        foreach ($argv as $arg) {
            // Cari string yang diawali dengan --showtime_id=
            if (str_starts_with($arg, '--showtime_id=')) {
                $showtimeId = explode('=', $arg)[1];
                break;
            }
        }

        // 2. Buat Query
        $query = Showtime::with('ruangan');

        if ($showtimeId) {
            // Jika ID ditemukan, filter query hanya untuk ID tersebut
            $query->where('id', $showtimeId);
            $this->command->info("Seeding hanya untuk Showtime ID spesifik: {$showtimeId}");
        } else {
            // Jika tidak ada ID, ambil semua showtime (perilaku default)
            $this->command->warn('❌ Tidak ada --showtime_id yang diberikan. Seeding untuk SEMUA showtime yang ada.');
        }

        $showtimes = $query->get();

        if ($showtimes->isEmpty()) {
            $this->command->info('❌ Tidak ada showtime yang ditemukan.');
            return;
        }

        $created = 0;

        foreach ($showtimes as $showtime) {
            // 3. Hapus kursi LAMA HANYA untuk showtime ini
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

            // Lakukan mass insert
            Seat::insert($seatsToInsert);
            $created += $count;
            $this->command->info("✔ Kursi untuk Showtime ID {$showtime->id} (Ruangan {$showtime->ruangan->nama}) berhasil dibuat: {$count} (Menghapus {$deletedCount} kursi lama).");
        }

        $this->command->info("🎉 Total kursi dibuat: {$created}");
    }
}
