<x-layout>
    <h1 class="text-2xl font-bold mb-4">Buat Pesanan Baru</h1>

    <p>Film: {{ $showtime->film->judul }}</p>
    <p>Waktu Tayang: {{ $showtime->waktu }}</p>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <label for="seat">Pilih Kursi:</label>
        <select name="seat_id" id="seat" required>
            @foreach($seats as $seat)
                <option value="{{ $seat->id }}">
                    Kursi {{ $seat->nomor }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
            Pesan
        </button>
    </form>
</x-layout>
