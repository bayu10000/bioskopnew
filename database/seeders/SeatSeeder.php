<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $ruangans = Ruangan::all();

        if ($ruangans->isEmpty()) {
            $this->command->info('Tidak ada ruangan. Jalankan RuanganSeeder terlebih dahulu.');
            return;
        }

        // Nonaktifkan FK sementara untuk operasi hapus massal
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Seat::query()->delete();
        DB::statement('ALTER TABLE `seats` AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Denah kursi
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $columns = 20;
        $harga_tiket = 30000;

        $created = 0;
        foreach ($ruangans as $ruangan) {
            foreach ($rows as $row) {
                for ($col = 1; $col <= $columns; $col++) {
                    Seat::create([
                        'ruangan_id' => $ruangan->id,
                        'nomor_kursi' => $row . $col,
                        'status' => 'available',
                        'harga' => $harga_tiket,
                    ]);
                    $created++;
                }
            }
        }

        $this->command->info("Seat seeded: {$created} seats across {$ruangans->count()} ruangan(s).");
    }
}
