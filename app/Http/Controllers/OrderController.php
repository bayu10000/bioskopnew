<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Order;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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
            'selected_seats.*' => 'required|string|max:10|exists:seats,nomor_kursi',
        ]);

        $showtimeId = $request->input('showtime_id');
        $selectedSeatCodes = $request->input('selected_seats');

        DB::beginTransaction();

        try {
            $showtime = Showtime::find($showtimeId);

            $seats = Seat::whereIn('nomor_kursi', $selectedSeatCodes)
                ->where('showtime_id', $showtimeId)
                ->get();

            if ($seats->count() !== count($selectedSeatCodes)) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Ada kesalahan dalam pemilihan kursi atau kursi tidak valid.');
            }

            $unavailableSeat = $seats->first(fn($seat) => $seat->status !== 'available');

            if ($unavailableSeat) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Salah satu kursi yang Anda pilih sudah tidak tersedia.');
            }

            $selectedSeatIds = $seats->pluck('id')->toArray();
            $totalPrice = $showtime->harga * count($selectedSeatIds);

            // PERBAIKAN: Buat hash unik untuk QR Code
            $qrCodeHash = Str::uuid()->toString();

            // Buat pesanan baru
            $order = Order::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtimeId,
                'jumlah_tiket' => count($selectedSeatIds),
                'total_harga' => $totalPrice,
                'status' => 'pending',
                'qr_code_hash' => $qrCodeHash, // SIMPAN HASH
            ]);

            if (!$order) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal membuat pesanan. Silakan coba lagi.');
            }

            // Perbarui status kursi menjadi 'booked'
            Seat::whereIn('id', $selectedSeatIds)
                ->update(['status' => 'booked']);

            // Lampirkan kursi ke pesanan
            $order->seats()->attach($selectedSeatIds);

            DB::commit();

            return redirect()->route('my-orders')->with('success', 'Pemesanan berhasil! Pembayaran dapat dilakukan dalam 24 jam.');
        } catch (\Exception $e) {
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

    public function viewTicket($hash)
    {
        // Cari pesanan berdasarkan qr_code_hash
        $order = Order::with(['user', 'seats', 'showtime.film', 'showtime.ruangan'])
            ->where('qr_code_hash', $hash)
            ->firstOrFail(); // Jika tidak ada, kembalikan 404

        // Tampilan ini khusus untuk tiket yang di-print/di-scan
        return view('frontend.ticket-print', compact('order'));
    }

    // ----------------------------------------------------
    // FUNGSI HELPER UNTUK MEMBEBASKAN KURSI (REUSABLE)
    // ----------------------------------------------------
    /**
     * Fungsi Helper untuk membebaskan kursi dan mengubah status pesanan menjadi 'cancelled'.
     *
     * @param Order $order
     * @return bool True jika berhasil, False jika gagal.
     */
    // ... (Import dan class tetap sama)

    // ----------------------------------------------------
    // FUNGSI HELPER UNTUK MEMBEBASKAN KURSI (CANCEL)
    // ----------------------------------------------------
    public static function lockSeats(Order $order, string $newStatus): bool
    {
        if (!in_array($newStatus, ['paid', 'done']) || $order->status === $newStatus) {
            return true;
        }

        try {
            DB::beginTransaction();

            // 1. Ubah status kursi menjadi 'booked'
            $seatIds = $order->seats->pluck('id')->toArray();
            Seat::whereIn('id', $seatIds)->update(['status' => 'booked']);

            // 2. Ubah status order
            $order->status = $newStatus;
            $order->save(); // ⬅️ Ini akan memicu Order::booted()->static::updated()

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal mengunci kursi/mengaktifkan pesanan: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------
    // 💡 FIX: Hapus panggilan Rekap manual
    // ----------------------------------------------------
    public static function releaseSeats(Order $order): bool
    {
        if ($order->status === 'cancelled') {
            return true;
        }

        try {
            DB::beginTransaction();

            // 1. Ubah status kursi menjadi 'available'
            $seatIds = $order->seats->pluck('id')->toArray();
            Seat::whereIn('id', $seatIds)->update(['status' => 'available']);

            // 2. Ubah status order menjadi 'cancelled'
            $order->status = 'cancelled';
            $order->save(); // ⬅️ Ini akan memicu Order::booted()->static::updated()

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal membebaskan kursi/membatalkan pesanan: " . $e->getMessage());
            return false;
        }
    }

    // ----------------------------------------------------

    // ----------------------------------------------------
    // FUNGSI MARK AS DONE (PELANGGAN)
    // ----------------------------------------------------
    // ... (Kode di atas tetap sama)

    // ----------------------------------------------------
    // FUNGSI MARK AS DONE (PELANGGAN)
    // ----------------------------------------------------
    public function markAsDone($orderId)
    {
        $order = Order::with('showtime')
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $showtimeDateTime = Carbon::parse($order->showtime->tanggal . ' ' . $order->showtime->jam);
        $currentDateTime = Carbon::now();

        // Kondisi: Hanya PAID DAN jadwal sudah terlewat
        $canMarkAsDone = ($order->status === 'paid') && $showtimeDateTime->lessThan($currentDateTime);

        if (!$canMarkAsDone) {
            return redirect()->back()->with('error', 'Pesanan belum lunas atau jadwal tayang belum terlewat.');
        }

        // 💡 PERBAIKAN PENTING: Gunakan tanda kutip tunggal ('done') secara eksplisit 
        // untuk memastikan database membaca nilai sebagai string.
        $order->status = 'done'; // Pastikan nilai ini berupa string
        $order->save();

        return redirect()->route('my-orders')->with('success', 'Konfirmasi menonton berhasil! Pesanan #' . $orderId . ' telah ditandai sebagai **Selesai Ditonton**.');
    }
    // ... (Kode di bawah tetap sama)
    // ----------------------------------------------------

    // ----------------------------------------------------
    // FUNGSI cancelOrder (PELANGGAN)
    // ----------------------------------------------------
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::with('showtime') // Eager load showtime untuk cek tanggal
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cek status dan waktu tayang
        $showtimeDateTime = Carbon::parse($order->showtime->tanggal . ' ' . $order->showtime->jam);

        // 💡 PERBAIKAN: Hanya izinkan pembatalan jika status 'pending' DAN jadwal BELUM terlewat
        $canCancel = in_array($order->status, ['pending']) && $showtimeDateTime->isFuture();

        if ($order->status == 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        if (!$canCancel) {
            return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan (Harus status Pending dan jadwal belum lewat).');
        }

        // Panggil fungsi helper
        if (self::releaseSeats($order)) {
            return redirect()->route('my-orders')->with('success', 'Pesanan #' . $orderId . ' berhasil **dibatalkan**. Kursi sudah **tersedia** kembali.');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan. Terjadi kesalahan sistem.');
        }
    }
}
