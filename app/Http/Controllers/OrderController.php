<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Order;
use App\Models\Showtime;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create($showtime_id)
    {
        $showtime = Showtime::with('film')->findOrFail($showtime_id);
        $seats = Seat::where('showtime_id', $showtime_id)->get();

        return view('orders.create', compact('showtime', 'seats'));
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

        // Simpan order (contoh)


        $order = Order::create([
            'user_id'      => Auth::id(),
            'showtime_id'  => $showtime->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga'  => $showtime->harga * $request->jumlah_tiket,
            'tanggal'      => $showtime->tanggal,
            'jam'           => $showtime->jam,
            // isi dari showtime
            'status'       => 'pending',
        ]);


        foreach ($request->kursi as $seatId) {
            OrderDetail::create([
                'order_id' => $order->id,
                'seat_id'  => $seatId,
                'harga'    => $showtime->harga
            ]);

            // Update status kursi jadi booked
            Seat::where('id', $seatId)->update(['status' => 'booked']);
        }

        return redirect()->route('orderSuccess', $order->id);
    }
}
