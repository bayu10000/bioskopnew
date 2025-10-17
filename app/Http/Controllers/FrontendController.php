<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    /**
     * Tampilkan halaman utama dengan daftar film.
     */
    public function index(Request $request)
    {
        // Ambil waktu saat ini
        $currentDateTime = Carbon::now();

        // 💡 PERBAIKAN 1: Eager load showtimes DENGAN FILTER waktu.
        // Ini memastikan `$film->showtimes` hanya berisi jadwal yang belum terlewat,
        // sehingga hitungan di index.blade.php menjadi akurat.
        $query = Film::with(['genres', 'showtimes' => function ($q) use ($currentDateTime) {
            // Filter: Hanya ambil yang waktu tayangnya di masa depan (lebih dari sekarang)
            $q->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')]);
        }]);

        // Filter berdasarkan genre (jika dipilih)
        if ($request->filled('genre')) {
            $genreId = $request->input('genre');
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        // Logika Search (judul & sinopsis misalnya)
        if ($request->filled('search')) {
            $keyword = $request->input('search');
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', '%' . $keyword . '%')
                    ->orWhere('sinopsis', 'like', '%' . $keyword . '%');
            });
        }

        // 💡 PERBAIKAN 2: Filter Film secara keseluruhan.
        // HANYA tampilkan film yang MASIH MEMILIKI JADWAL TAYANG DI MASA DEPAN.
        // Ini yang menyebabkan film akan hilang dari halaman utama jika semua jadwalnya terlewat.
        $query->whereHas('showtimes', function ($q) use ($currentDateTime) {
            $q->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')]);
        });


        // Ambil semua genre untuk filter di view
        $genres = Genre::all();

        // Pagination + appends query biar param search/genre tetap ada
        $films = $query->orderBy('created_at', 'desc')
            ->paginate(9)
            ->appends($request->only(['search', 'genre']));

        return view('frontend.index', compact('films', 'genres'));
    }

    // ----------------------------------------------------
    // FUNGSI showFilm: Menampilkan detail film dan jadwal
    // ----------------------------------------------------

    /**
     * Tampilkan detail film dengan jadwal tayang.
     */
    public function showFilm($id, Request $request)
    {
        $film = Film::with('genres')->findOrFail($id);
        $currentDateTime = Carbon::now(); // Ambil waktu saat ini

        // 1. Ambil tanggal tayang yang unik dan BELUM terlewat
        $showtimeDates = $film->showtimes()
            // Filter: Hanya ambil yang waktu tayangnya di masa depan (lebih dari sekarang)
            // Menggunakan '>' alih-alih '>=' untuk menghilangkan jadwal yang sudah dimulai/lewat.
            ->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')])
            ->distinct()
            ->pluck('tanggal')
            ->sortBy(fn($date) => Carbon::parse($date));

        // 2. Tentukan tanggal yang dipilih (jika ada, gunakan yang paling awal)
        $selectedDate = $request->input('date', $showtimeDates->first());

        $showtimes = collect();
        if ($selectedDate) {
            // 3. Ambil jadwal tayang spesifik untuk tanggal yang dipilih dan BELUM terlewat
            $query = Showtime::with('ruangan')
                ->where('film_id', $film->id)
                ->where('tanggal', $selectedDate)
                // Filter: Hanya ambil yang waktu tayangnya di masa depan
                ->whereRaw("CONCAT(tanggal, ' ', jam) > ?", [$currentDateTime->format('Y-m-d H:i:s')]);

            $showtimes = $query->orderBy('jam')
                ->get();
        }

        return view('frontend.film', compact('film', 'showtimes', 'selectedDate', 'showtimeDates'));
    }

    // ----------------------------------------------------
    // FUNGSI profile: Menampilkan profil pengguna
    // ----------------------------------------------------

    /**
     * Tampilkan halaman profil pengguna.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }
}
