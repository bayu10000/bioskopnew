<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Home - Bioskop</title></head>
<body>
    <h1>Daftar Film</h1>

    @if(session('success'))
        <div style="color:green">{{ session('success') }}</div>
    @endif

    @if(Auth::check())
        <p>Halo, {{ Auth::user()->name }} | <form style="display:inline" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form></p>
    @else
        <p><a href="{{ route('login.form') }}">Login</a> | <a href="{{ route('register.form') }}">Register</a></p>
    @endif

    <ul>
        @forelse($films as $film)
            <li>
                <a href="{{ route('film.show', $film->id) }}">{{ $film->judul ?? $film->title ?? 'Judul' }}</a>
                @if($film->showtimes ?? false)
                    <ul>
                        @foreach($film->showtimes as $st)
                            <li>{{ $st->tanggal ?? '' }} {{ $st->jam ?? '' }} - <a href="{{ route('order', $st->id) }}">Pesan</a></li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @empty
            <li>Tidak ada film.</li>
        @endforelse
    </ul>
</body>
</html>
