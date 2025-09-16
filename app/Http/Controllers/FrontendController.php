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
        $query = Film::query();

        // Logika Search
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->input('search') . '%');
        }

        // Ambil semua genre unik
        $genres = Genre::all();

        // Filter berdasarkan genre
        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->input('genre'));
            });
        }

        $films = $query->paginate(9);

        return view('frontend.index', compact('films', 'genres'));
    }

    /**
     * Tampilkan detail film dengan jadwal tayang.
     */
    public function showFilm($id, Request $request)
    {
        // Temukan film berdasarkan ID atau tampilkan 404 jika tidak ditemukan
        $film = Film::with('genres')->findOrFail($id);

        // Ambil semua tanggal unik yang memiliki jadwal tayang untuk film ini, urutkan
        $showtimeDates = $film->showtimes()
            ->distinct()
            ->pluck('tanggal')
            ->sortBy(function ($date) {
                return Carbon::parse($date);
            });

        $selectedDate = null;
        $showtimes = collect();

        // Jika ada tanggal yang dipilih dari form
        if ($request->filled('date')) {
            $selectedDate = $request->input('date');
        } else {
            // Jika tidak ada tanggal yang dipilih, pilih tanggal pertama dari daftar
            $selectedDate = $showtimeDates->first();
        }

        // Jika ada tanggal yang dipilih, ambil jadwal tayangnya
        if ($selectedDate) {
            $showtimes = Showtime::with('ruangan')
                ->where('film_id', $film->id)
                ->where('tanggal', $selectedDate)
                ->orderBy('jam')
                ->get();
        }

        return view('frontend.film', compact('film', 'showtimes', 'selectedDate', 'showtimeDates'));
    }

    /**
     * Tampilkan halaman profil pengguna.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('frontend.profile', compact('user'));
    }
}
