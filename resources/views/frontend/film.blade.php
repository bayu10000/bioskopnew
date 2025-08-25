@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $film->judul }}</h1>

    <img src="{{ asset('storage/' . $film->poster) }}" alt="Poster {{ $film->judul }}" width="300">

    <p>{{ $film->sinopsis }}</p>
    <p><strong>Genre:</strong> {{ $film->genre }}</p>
    <p><strong>Durasi:</strong> {{ $film->durasi }} menit</p>

    @if($film->link_trailer)
        <p>
            <a href="{{ $film->link_trailer }}" target="_blank" class="btn btn-primary">🎬 Lihat Trailer</a>
        </p>
    @endif

    <h3>Jadwal Tayang</h3>
    @if($showtimes->count() > 0)
        <ul>
            @foreach($showtimes as $showtime)
                <li>
                    {{ $showtime->waktu_tayang }}  
                    <a href="{{ url('/order/' . $showtime->id) }}" class="btn btn-success btn-sm">Pesan Tiket</a>
                </li>
            @endforeach
        </ul>
    @else
        <p>Tidak ada jadwal tayang tersedia.</p>
    @endif

    <p>
        <a href="{{ route('home') }}" class="btn btn-secondary">⬅ Kembali</a>
    </p>
</div>
@endsection
