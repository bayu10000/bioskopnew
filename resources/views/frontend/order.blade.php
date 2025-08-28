@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pemesanan Tiket</h1>

    <h3>Film: {{ $showtime->film->judul }}</h3>
    <p>Jam Tayang: {{ $showtime->tanggal }} {{ $showtime->jam }}</p>
    <p>Harga per Tiket: Rp {{ number_format($showtime->harga, 0, ',', '.') }}</p>
    <p>Tersedia: <strong>{{ $availableCount }}</strong> kursi</p>

    @if ($errors->any())
        <div style="color:#b91c1c; margin-bottom:10px;">
            @foreach ($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('order.store') }}" method="POST" id="orderForm">
        @csrf
        <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
        <input type="hidden" id="harga_tiket" value="{{ $showtime->harga }}">

        {{-- Input jumlah tiket --}}
        <div style="margin:10px 0;">
            <label for="jumlah_tiket">Jumlah Tiket</label>
            <input
                type="number"
                id="jumlah_tiket"
                name="jumlah_tiket"
                min="1"
                max="{{ $availableCount }}"
                value="{{ old('jumlah_tiket', 1) }}"
                required
                style="margin-left:8px;width:100px;"
            >
            <small>(maks: {{ $availableCount }})</small>
        </div>

        {{-- Pilihan kursi --}}
        <div id="seat-selection" style="margin:10px 0;"></div>

        {{-- Total harga --}}
        <div style="margin:10px 0;">
            <label>Total Harga:</label>
            <input type="text" id="total_harga" value="Rp {{ number_format($showtime->harga,0,',','.') }}" readonly style="margin-left:8px;">
        </div>

        <button type="submit" class="btn btn-primary">Pesan</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const harga   = parseInt(document.getElementById('harga_tiket').value, 10);
    const qty     = document.getElementById('jumlah_tiket');
    const total   = document.getElementById('total_harga');
    const seatDiv = document.getElementById('seat-selection');

    // Ambil data kursi yang masih available dari Laravel
    const seats = @json($showtime->seats->where('status', 'available')->pluck('nomor_kursi', 'id'));

    function formatRupiah(n) {
        return 'Rp ' + (n || 0).toLocaleString('id-ID');
    }

    function updateTotal() {
        const j = Math.max(1, parseInt(qty.value || '1', 10));
        total.value = formatRupiah(j * harga);
        generateSeatDropdown(j);
    }

    function generateSeatDropdown(count) {
        seatDiv.innerHTML = ''; // kosongkan isi

        for (let i = 1; i <= count; i++) {
            const wrapper = document.createElement('div');
            wrapper.style.margin = '5px 0';

            const label = document.createElement('label');
            label.textContent = `Kursi ${i}: `;
            wrapper.appendChild(label);

            const select = document.createElement('select');
            select.name = 'kursi[]';
            select.required = true;
            select.style.marginLeft = '8px';

            // Option default
            const optDefault = document.createElement('option');
            optDefault.value = '';
            optDefault.textContent = `Pilih Kursi ${i}`;
            select.appendChild(optDefault);

            // Tambahkan kursi yang tersedia
            for (const [id, nomor] of Object.entries(seats)) {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = nomor;
                select.appendChild(opt);
            }

            wrapper.appendChild(select);
            seatDiv.appendChild(wrapper);
        }

        // Pastikan kursi tidak bisa dipilih ganda
        seatDiv.querySelectorAll('select').forEach(sel => {
            sel.addEventListener('change', preventDuplicate);
        });
    }

    function preventDuplicate() {
        const selected = Array.from(seatDiv.querySelectorAll('select'))
            .map(sel => sel.value)
            .filter(v => v !== '');

        seatDiv.querySelectorAll('select').forEach(sel => {
            Array.from(sel.options).forEach(opt => {
                if (opt.value !== '' && selected.includes(opt.value) && sel.value !== opt.value) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });
    }

    qty.addEventListener('input', updateTotal);
    updateTotal(); // jalankan saat awal
});
</script>
@endsection