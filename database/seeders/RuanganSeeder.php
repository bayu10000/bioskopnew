<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ruanganData = [
            ['nama' => 'Ruangan 1', 'kapasitas' => 200],
            ['nama' => 'Ruangan 2', 'kapasitas' => 200],
            ['nama' => 'Ruangan 3', 'kapasitas' => 200],
            ['nama' => 'Ruangan 4', 'kapasitas' => 200],
            ['nama' => 'Ruangan 5', 'kapasitas' => 200],
        ];

        // Gunakan updateOrCreate agar aman dari foreign key dan tidak butuh truncate
        foreach ($ruanganData as $data) {
            Ruangan::updateOrCreate(
                ['nama' => $data['nama']], // unik berdasarkan nama
                $data
            );
        }

        $this->command->info('Ruangan seeded: ' . Ruangan::count());
    }
}
