<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function show($showtimeId)
    {
        $showtime = Showtime::with(['film', 'ruangan', 'seats'])->findOrFail($showtimeId);
        $availableCount = $showtime->seats()->where('status', 'available')->count();

        $seatsForJs = $showtime->seats->map(fn($seat) => [
            'id' => $seat->id,
            'nomor_kursi' => $seat->nomor_kursi,
            'status' => $seat->status,
        ])->values();

        // Tambahkan logika warna/icon ruangan
        $warnaRuangan = match ($showtime->ruangan->nama) {
            'Studio 1' => 'fa fa-door-open text-danger',
            'Studio 2' => 'fa fa-door-open text-success',
            'Studio 3' => 'fa fa-door-open text-warning',
            default    => 'fa fa-door-open text-primary',
        };

        return view('frontend.order', compact(
            'showtime',
            'availableCount',
            'seatsForJs',
            'warnaRuangan'
        ));
    }


    public function store(Request $request)
    {
        // 1. Validasi permintaan
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'selected_seats' => 'required|array|min:1',
            'selected_seats.*' => 'required|exists:seats,id',
        ]);

        $showtimeId = $request->input('showtime_id');
        $selectedSeatIds = $request->input('selected_seats');

        // Gunakan transaksi untuk memastikan semua operasi berhasil atau tidak sama sekali
        DB::beginTransaction();

        try {
            // Ambil data showtime dan kursi di dalam transaksi
            $showtime = Showtime::find($showtimeId);
            $seats = Seat::whereIn('id', $selectedSeatIds)->get();

            // Verifikasi kursi masih tersedia dan milik showtime yang sama
            foreach ($seats as $seat) {
                if ($seat->showtime_id != $showtimeId || $seat->status !== 'available') {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Salah satu kursi yang Anda pilih sudah tidak tersedia.');
                }
            }

            // Hitung total harga
            $totalPrice = $showtime->harga * count($selectedSeatIds);

            // Buat pesanan baru
            $order = Order::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtimeId,
                'jumlah_tiket' => count($selectedSeatIds),
                'total_harga' => $totalPrice,
                'status' => 'pending',
            ]);

            // Periksa apakah pesanan berhasil dibuat
            if (!$order) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
            }

            // Perbarui status kursi menjadi 'booked'
            Seat::whereIn('id', $selectedSeatIds)
                ->update(['status' => 'booked']);

            // Lampirkan kursi ke pesanan melalui tabel pivot
            // Ini akan secara otomatis mengisi 'order_id' dan 'seat_id'
            $order->seats()->attach($selectedSeatIds);

            // Jika semua operasi berhasil, commit transaksi
            DB::commit();

            return redirect()->route('my-orders')->with('success', 'Pemesanan berhasil!');
        } catch (\Exception $e) {
            // Jika ada kesalahan, batalkan semua operasi
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function myOrders()
    {
        $orders = Order::with(['user', 'seats', 'showtime.film', 'showtime.ruangan'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.my-orders', compact('orders'));
    }
}
