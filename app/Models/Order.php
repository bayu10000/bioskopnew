<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rekap;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'showtime_id',
        'jumlah_tiket',
        'total_harga',
        'status',
        'created_at',
        'qr_code_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'order_details')->withTimestamps();
    }

    // ====================================================================
    // FUNGSI HELPER REKAP (TETAP SAMA)
    // ====================================================================

    private static function getRekapRecord(Order $order)
    {
        $order->loadMissing('showtime.film', 'showtime.ruangan');
        $showtime = $order->showtime;

        if (!$showtime || !$showtime->film || !$showtime->ruangan) {
            return null;
        }

        $uniqueKey = [
            'film_id'    => $showtime->film_id,
            'tanggal'    => $showtime->tanggal,
            'ruangan'    => $showtime->ruangan->nama,
            'jam_tayang' => Carbon::parse($showtime->jam)->format('H:i:s'),
        ];

        return Rekap::firstOrCreate($uniqueKey);
    }

    public static function updateRekapOnPaid(Order $order)
    {
        $rekap = self::getRekapRecord($order);
        if (!$rekap) return;

        $orderTotals = Order::select(
            DB::raw('SUM(jumlah_tiket) as total_tiket'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->where('showtime_id', $order->showtime_id)
            ->whereIn('status', ['paid', 'done'])
            ->first();

        $rekap->total_tiket_terjual = $orderTotals->total_tiket ?? 0;
        $rekap->total_pendapatan = $orderTotals->total_pendapatan ?? 0.00;
        $rekap->save();
    }

    public static function updateRekapOnCancelled(Order $order)
    {
        $rekap = self::getRekapRecord($order);
        if (!$rekap) return;

        $orderTotals = Order::select(
            DB::raw('SUM(jumlah_tiket) as total_tiket'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->where('showtime_id', $order->showtime_id)
            ->whereIn('status', ['paid', 'done'])
            ->first();

        if ($orderTotals && ($orderTotals->total_tiket > 0 || $orderTotals->total_pendapatan > 0)) {
            $rekap->total_tiket_terjual = $orderTotals->total_tiket;
            $rekap->total_pendapatan = $orderTotals->total_pendapatan;
            $rekap->save();
        } else {
            $rekap->delete();
        }
    }

    // ====================================================================
    // 💡 FIX: Auto-Sync via Model Event
    // ====================================================================
    protected static function booted()
    {
        static::updated(function (Order $order) {
            // Hanya jalankan jika kolom 'status' telah berubah
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                // Transisi: dari status Paid/Done ke Cancelled (Kurangi Rekap)
                if ($newStatus === 'cancelled' && in_array($oldStatus, ['paid', 'done'])) {
                    self::updateRekapOnCancelled($order);
                }

                // Transisi: dari Pending ke Paid atau Paid ke Done (Tambah/Update Rekap)
                elseif (in_array($newStatus, ['paid', 'done']) && in_array($oldStatus, ['pending', 'paid'])) {
                    self::updateRekapOnPaid($order);
                }
            }
        });
    }
}
