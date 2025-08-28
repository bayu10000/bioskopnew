<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Showtime;
use App\Models\Order;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function index()
    {
        $films = Film::all();
        return view('frontend.index', compact('films'));
    }

    public function showFilm($id)
    {
        $film = Film::findOrFail($id);
        $showtimes = Showtime::where('film_id', $id)
            ->orderBy('tanggal')
            ->orderBy('jam')
            ->get();

        return view('frontend.film', compact('film', 'showtimes'));
    }

    // ✅ Perbaikan: Tambahkan $availableCount
    public function order($showtimeId)
    {
        $showtime = Showtime::with('film', 'seats')->findOrFail($showtimeId);
        $seats = $showtime->seats;
        $availableCount = $seats->where('status', 'available')->count();

        return view('frontend.order', compact('showtime', 'seats', 'availableCount'));
    }

    public function myOrders()
    {
        $orders = Order::with('showtime.film', 'showtime.ruangan', 'seats')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.my-orders', compact('orders'));
    }

    public function storeOrder(Request $request)
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

        $invalidSeats = Seat::whereIn('id', $request->kursi)
            ->where('status', '!=', 'available')
            ->count();

        if ($invalidSeats > 0) {
            return back()->withErrors(['kursi' => 'Ada kursi yang sudah terbooking. Silakan pilih ulang.'])->withInput();
        }

        $order = Order::create([
            'user_id'      => Auth::id(),
            'showtime_id'  => $showtime->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga'  => $totalHarga,
            'status'       => 'pending',
        ]);

        $order->seats()->sync($request->kursi);

        Seat::whereIn('id', $request->kursi)->update(['status' => 'booked']);

        return redirect()->route('my-orders')->with('success', 'Pemesanan berhasil!');
    }
}
