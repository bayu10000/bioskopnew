<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Showtime;
use App\Models\Order;
use App\Models\OrderDetail;
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

    // Form pemesanan
    public function order($showtimeId)
    {
        $showtime = Showtime::with('film')->findOrFail($showtimeId);

        $availableCount = Seat::where('showtime_id', $showtime->id)
            ->where('status', 'available')
            ->count();

        return view('frontend.order', [
            'showtime'       => $showtime,
            'availableCount' => $availableCount,
        ]);
    }

    // Simpan pesanan + auto-assign kursi
    public function storeOrder(Request $request)
    {
        $request->validate([
            'showtime_id'   => 'required|exists:showtimes,id',
            'jumlah_tiket'  => 'required|integer|min:1',
        ]);

        $showtime = Showtime::findOrFail($request->showtime_id);

        // Cari kursi available sesuai jumlah tiket
        $seats = Seat::where('showtime_id', $showtime->id)
            ->where('status', 'available')
            ->orderBy('nomor_kursi')
            ->take($request->jumlah_tiket)
            ->get();

        if ($seats->count() < $request->jumlah_tiket) {
            return back()->withErrors([
                'jumlah_tiket' => 'Kursi tidak mencukupi. Tersisa: ' . $seats->count() . ' kursi.',
            ])->withInput();
        }

        // Buat order
        $order = Order::create([
            'user_id'      => Auth::id(),
            'showtime_id'  => $showtime->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga'  => $showtime->harga * $request->jumlah_tiket,
            'tanggal'      => $showtime->tanggal, // optional: bisa dihapus juga, karena sudah ada di showtime
            'status'       => 'pending',
        ]);

        // Simpan detail kursi + tandai kursi booked
        foreach ($seats as $seat) {
            OrderDetail::create([
                'order_id'    => $order->id,
                'seat_id'     => $seat->id,
                'showtime_id' => $showtime->id,
                'user_id'     => Auth::id(),
            ]);

            $seat->update(['status' => 'booked']);
        }

        $kursiText = $seats->pluck('nomor_kursi')->join(', ');

        return redirect()->route('home')
            ->with('success', "Pemesanan berhasil. Kursi Anda: {$kursiText} (Total Rp " .
                number_format($order->total_harga, 0, ',', '.') . ")");
    }
}
