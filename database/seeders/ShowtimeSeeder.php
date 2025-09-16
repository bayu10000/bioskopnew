<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Film;
use App\Models\Ruangan;
use App\Models\Showtime;
use App\Models\Seat;
use Illuminate\Support\Carbon;

class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data film dan ruangan yang sudah ada
        $films = Film::all();
        $ruangans = Ruangan::all();

        // Pastikan ada film dan ruangan di database
        if ($films->isEmpty() || $ruangans->isEmpty()) {
            $this->command->info('Tidak ada film atau ruangan. Jalankan seeder Film dan Ruangan terlebih dahulu.');
            return;
        }

        // Tentukan jumlah showtime yang ingin dibuat
        $numberOfShowtimes = 10;
        $currentDate = Carbon::now();

        for ($i = 0; $i < $numberOfShowtimes; $i++) {
            // Pilih film dan ruangan secara acak
            $film = $films->random();
            $ruangan = $ruangans->random();

            // Tentukan tanggal dan jam tayang
            $tanggal = $currentDate->addDays(rand(1, 3)); // Jadwal 1-3 hari ke depan
            $jam = ['10:00', '13:00', '16:00', '19:00', '21:00'][array_rand(['10:00', '13:00', '16:00', '19:00', '21:00'])];
            $harga = rand(30000, 50000);

            // Buat entri Showtime
            $showtime = Showtime::create([
                'film_id' => $film->id,
                'ruangan_id' => $ruangan->id,
                'tanggal' => $tanggal->toDateString(),
                'jam' => $jam,
                'harga' => $harga,
            ]);

            // Buat entri Kursi (Seat) untuk showtime ini
            $this->seedSeatsForShowtime($showtime, $ruangan->kapasitas);
        }

        $this->command->info('Data Showtime dan Seat berhasil di-seed.');
    }

    /**
     * Buat data kursi untuk setiap showtime berdasarkan kapasitas ruangan.
     */
    private function seedSeatsForShowtime(Showtime $showtime, int $kapasitas)
    {
        for ($i = 1; $i <= $kapasitas; $i++) {
            Seat::create([
                'showtime_id' => $showtime->id,
                'nomor_kursi' => 'A' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'available',
            ]);
        }
    }
}
