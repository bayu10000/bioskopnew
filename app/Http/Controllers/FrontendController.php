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
        $query = Film::with(['genres', 'showtimes']); // eager load biar tidak N+1

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

        // Ambil semua genre untuk filter di view
        $genres = Genre::all();

        // Pagination + appends query biar param search/genre tetap ada
        $films = $query->orderBy('created_at', 'desc')
            ->paginate(9)
            ->appends($request->only(['search', 'genre']));

        return view('frontend.index', compact('films', 'genres'));
    }

    /**
     * Tampilkan detail film dengan jadwal tayang.
     */
    public function showFilm($id, Request $request)
    {
        $film = Film::with('genres')->findOrFail($id);

        $showtimeDates = $film->showtimes()
            ->distinct()
            ->pluck('tanggal')
            ->sortBy(fn($date) => Carbon::parse($date));

        $selectedDate = $request->input('date', $showtimeDates->first());

        $showtimes = collect();
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
