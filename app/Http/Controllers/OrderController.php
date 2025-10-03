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
        // 🚨 PERBAIKAN: Validasi sekarang memeriksa keberadaan kode kursi di kolom 'nomor_kursi'
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'selected_seats' => 'required|array|min:1',
            // Memastikan kode kursi yang dikirim ada di tabel seats pada kolom 'nomor_kursi'
            'selected_seats.*' => 'required|string|max:10|exists:seats,nomor_kursi',
        ]);

        $showtimeId = $request->input('showtime_id');
        // 🚨 $selectedSeatCodes sekarang berisi KODE KURSI (misalnya ['A1', 'B2'])
        $selectedSeatCodes = $request->input('selected_seats');

        // Gunakan transaksi untuk memastikan semua operasi berhasil atau tidak sama sekali
        DB::beginTransaction();

        try {
            // Ambil data showtime
            $showtime = Showtime::find($showtimeId);

            // 🚨 PERBAIKAN: Ambil kursi berdasarkan NOMOR KURSI (kode)
            $seats = Seat::whereIn('nomor_kursi', $selectedSeatCodes)
                ->where('showtime_id', $showtimeId) // Pastikan hanya kursi showtime ini
                ->get();

            // Verifikasi jumlah kursi yang ditemukan sama dengan yang dipilih
            if ($seats->count() !== count($selectedSeatCodes)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Ada kesalahan dalam pemilihan kursi atau kursi tidak valid.');
            }

            // Verifikasi kursi masih tersedia
            $unavailableSeat = $seats->first(fn($seat) => $seat->status !== 'available');

            if ($unavailableSeat) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Salah satu kursi yang Anda pilih sudah tidak tersedia.');
            }

            // Ambil ID kursi
            $selectedSeatIds = $seats->pluck('id')->toArray();

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
            // Gunakan update pada koleksi yang sudah diambil agar tidak ada race condition pada kolom showtime_id
            Seat::whereIn('id', $selectedSeatIds)
                ->update(['status' => 'booked']);

            // Lampirkan kursi ke pesanan melalui tabel pivot
            // Ini akan secara otomatis mengisi 'order_id' dan 'seat_id'
            $order->seats()->attach($selectedSeatIds);

            // Jika semua operasi berhasil, commit transaksi
            DB::commit();

            return redirect()->route('my-orders')->with('success', 'Pemesanan berhasil!');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes (optional but recommended)
            // \Log::error('Order processing failed: ' . $e->getMessage()); 
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi. Error: ' . $e->getMessage());
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
