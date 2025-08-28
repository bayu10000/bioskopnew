<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function myOrders()
    {
        $orders = Order::with('seats', 'showtime.film')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.my-orders', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'showtime_id'   => 'required|exists:showtimes,id',
            'jumlah_tiket'  => 'required|integer|min:1',
            'kursi'         => 'required|array|min:1',
            'kursi.*'       => 'distinct|exists:seats,id'
        ], [
            'kursi.*.distinct' => 'Kursi tidak boleh sama.',
        ]);

        $showtime = Showtime::with('seats')->findOrFail($request->showtime_id);
        $totalHarga = $showtime->harga * $request->jumlah_tiket;

        // Pastikan kursi yang dipilih statusnya available
        $invalidSeats = Seat::whereIn('id', $request->kursi)
            ->where('status', '!=', 'available')
            ->count();

        if ($invalidSeats > 0) {
            return back()->withErrors(['kursi' => 'Ada kursi yang sudah terbooking. Silakan pilih ulang.'])->withInput();
        }

        if (count($request->kursi) != $request->jumlah_tiket) {
            return back()->withErrors(['jumlah_tiket' => 'Jumlah kursi harus sama dengan jumlah tiket.'])->withInput();
        }

        // Buat order utama
        $order = Order::create([
            'user_id'      => Auth::id(),
            'showtime_id'  => $showtime->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga'  => $totalHarga,
            'status'       => 'pending',
        ]);

        // Hubungkan order dengan kursi yang dipilih
        $order->seats()->sync($request->kursi);

        // Update status kursi menjadi 'booked'
        Seat::whereIn('id', $request->kursi)->update(['status' => 'booked']);

        $kursiText = Seat::whereIn('id', $request->kursi)->pluck('nomor_kursi')->implode(', ');

        return redirect()->route('my-orders')
            ->with('success', "Pemesanan berhasil! Kursi Anda: {$kursiText}.");
    }
}
