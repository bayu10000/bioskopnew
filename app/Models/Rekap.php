<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Rekap extends Model
{
    use HasFactory;

    protected $fillable = [
        'film_id',
        'tanggal',
        'ruangan',
        'jam_tayang',
        'total_tiket_terjual',
        'total_pendapatan',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * 💡 PERBAIKAN: Fungsi statis untuk menghitung ulang semua data rekap dari tabel orders.
     */
    public static function rebuildFromOrders(): int
    {
        // 1. Hapus semua data rekap yang ada saat ini
        self::truncate();

        // 2. Hitung agregat dari tabel orders (tanpa memuat relasi dulu)
        $ordersGrouped = Order::select(
            'showtime_id',
            DB::raw('SUM(jumlah_tiket) as total_tiket'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            // 💡 KUNCI FIX: Filter HANYA status yang Anda inginkan
            ->whereIn('status', ['paid', 'done'])
            ->groupBy('showtime_id')
            ->get();

        // 3. Ambil semua showtime yang terlibat secara efisien (Eager Loading)
        $showtimeIds = $ordersGrouped->pluck('showtime_id')->unique();
        $showtimes = Showtime::with(['film', 'ruangan'])
            ->whereIn('id', $showtimeIds)
            ->get()
            ->keyBy('id'); // Keying by ID untuk pencarian cepat O(1)

        $recordsCount = 0;
        $dataToInsert = [];

        // 4. Proses agregat
        foreach ($ordersGrouped as $orderGroup) {
            $showtime = $showtimes->get($orderGroup->showtime_id);

            // Cek jika Showtimes dan relasinya valid
            // Data tidak akan masuk rekap jika salah satu relasi TIDAK DITEMUKAN (null)
            if ($showtime && $showtime->film && $showtime->ruangan) {

                $jamTayang = Carbon::parse($showtime->jam)->format('H:i:s');

                $dataToInsert[] = [
                    'film_id'             => $showtime->film_id,
                    'tanggal'             => $showtime->tanggal,
                    'ruangan'             => $showtime->ruangan->nama,
                    'jam_tayang'          => $jamTayang,
                    'total_tiket_terjual' => $orderGroup->total_tiket,
                    'total_pendapatan'    => $orderGroup->total_pendapatan,
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ];
                $recordsCount++;
            }
        }

        // 5. Gunakan insert massal untuk efisiensi
        if (!empty($dataToInsert)) {
            self::insert($dataToInsert);
        }

        return $recordsCount;
    }
}
