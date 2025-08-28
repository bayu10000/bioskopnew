<!DOCTYPE html>
<html>
<head><title>{{ $film->judul ?? 'Film' }}</title></head>
<body>
    <h1>{{ $film->judul ?? 'Judul' }}</h1>
    <p>{{ $film->deskripsi ?? '' }}</p>

    <h3>Showtimes</h3>
    <ul>
        @foreach($film->showtimes as $st)
            <li>{{ $st->tanggal }} {{ $st->jam }} - <a href="{{ route('order', $st->id) }}">Pesan</a></li>
        @endforeach
    </ul>

    <p><a href="{{ route('home') }}">Kembali</a></p>
</body>
</html>
