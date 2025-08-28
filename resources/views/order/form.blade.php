<!DOCTYPE html>
<html>
<head><title>Order - {{ $showtime->film->judul ?? '' }}</title></head>
<body>
    <h1>Pesan: {{ $showtime->film->judul ?? '' }}</h1>
    <p>Tanggal: {{ $showtime->tanggal }} | Jam: {{ $showtime->jam }}</p>

    <form method="POST" action="{{ route('storeOrder') }}">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
        <label>Jumlah tiket:
            <input type="number" name="jumlah_tiket" value="1" min="1" required>
        </label>
        <button type="submit">Pesan sekarang</button>
    </form>

    <p><a href="{{ route('home') }}">Kembali</a></p>
</body>
</html>
