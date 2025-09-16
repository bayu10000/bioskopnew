<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Menampilkan halaman pemesanan tiket untuk showtime tertentu.
     */
    public function show($showtimeId)
    {
        // Temukan showtime berdasarkan ID dan muat relasi yang diperlukan
        $showtime = Showtime::with('film', 'ruangan', 'seats')
            ->findOrFail($showtimeId);

        // Ambil kursi yang berstatus 'available'
        $availableSeats = $showtime->seats()->where('status', 'available')->get();
        $availableCount = $availableSeats->count();

        return view('frontend.order', compact('showtime', 'availableSeats', 'availableCount'));
    }

    /**
     * Simpan pesanan baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'showtime_id'   => 'required|exists:showtimes,id',
            'kursi'         => 'required|array|min:1',
            'kursi.*'       => 'distinct|exists:seats,id',
        ], [
            'kursi.required' => 'Anda harus memilih setidaknya satu kursi.',
            'kursi.min'      => 'Anda harus memilih setidaknya satu kursi.',
            'kursi.*.distinct' => 'Kursi tidak boleh sama.',
        ]);

        // Cari data showtime dan film terkait
        $showtime = Showtime::with('seats', 'film')->findOrFail($request->showtime_id);

        // Ambil jumlah tiket dari jumlah kursi yang dipilih
        $jumlahTiket = count($request->kursi);
        $totalHarga = $showtime->harga * $jumlahTiket;

        // Cek apakah ada kursi yang sudah tidak tersedia
        $invalidSeats = Seat::whereIn('id', $request->kursi)
            ->where('status', '!=', 'available')
            ->count();

        if ($invalidSeats > 0) {
            return back()->withErrors(['kursi' => 'Ada kursi yang sudah terbooking. Silakan pilih ulang.'])->withInput();
        }

        // Buat pesanan baru
        $order = Order::create([
            'user_id'      => Auth::id(),
            'showtime_id'  => $showtime->id,
            'jumlah_tiket' => $jumlahTiket,
            'total_harga'  => $totalHarga,
            'status'       => 'pending',
            'tanggal'      => $showtime->tanggal,
            'jam'          => $showtime->jam,
        ]);

        // Hubungkan pesanan dengan kursi yang dipilih
        $order->seats()->sync($request->kursi);

        // Perbarui status kursi menjadi 'booked'
        Seat::whereIn('id', $request->kursi)->update(['status' => 'booked']);

        // Ambil nomor kursi untuk pesan sukses
        $kursiText = Seat::whereIn('id', $request->kursi)->pluck('nomor_kursi')->implode(', ');

        return redirect()->route('my-orders')->with('success', 'Pemesanan berhasil! Silakan lakukan pembayaran untuk menyelesaikan transaksi. Nomor kursi Anda: ' . $kursiText);
    }

    public function myOrders()
    {
        $orders = Order::with('seats', 'showtime.film')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.my-orders', compact('orders'));
    }
}
