@extends('layouts.app')

@section('content')
    <h1>Daftar Film</h1>

    @foreach($films as $film)
        <div style="margin-bottom:20px;">
            <h2>{{ $film->judul }}</h2>

            @if($film->poster)
                <img src="{{ asset('storage/' . $film->poster) }}" 
                     alt="Poster {{ $film->judul }}" 
                     width="200">
            @endif

            <p>{{ $film->sinopsis }}</p>

            @if($film->link_trailer)
                <p>
                    <a href="{{ $film->link_trailer }}" target="_blank">🎬 Lihat Trailer</a>
                </p>
            @endif

            <p>
                {{-- Arahkan ke halaman detail film, bukan langsung order --}}
                <a href="{{ route('film.show', $film->id) }}">🛒 Pesan Tiket</a>
            </p>
        </div>
    @endforeach
@endsection
