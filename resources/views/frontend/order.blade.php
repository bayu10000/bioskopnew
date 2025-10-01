@extends('layouts.template')

@section('title', 'Pemesanan Tiket')

@section('content')
<section class="anime-details spad">
    <div class="container">
        <div class="row">
            {{-- Menggunakan offset-lg-1 untuk memusatkan konten di desktop --}}
            <div class="col-lg-10 offset-lg-1 col-12"> 
                <div class="section-title">
                    <h4>Pemesanan Tiket</h4>
                </div>
                
                <div class="row">
                    {{-- Detail Film & Showtime (Side Card) --}}
                    {{-- Di layar besar/menengah (lg/md) 4 kolom, di layar kecil (sm/xs) 12 kolom --}}
                    <div class="col-lg-4 col-md-6 col-12 mb-4 order-md-1 order-2">
                        <div class="card h-100 bg-dark text-white order-summary-card">
                            <div class="card-body">
                                <h5 class="card-title text-danger">{{ $showtime->film->judul }}</h5>
                                <hr class="border-secondary">
                                
                                <p><i class="fa fa-calendar text-danger me-2"></i>
                                    {{ \Carbon\Carbon::parse($showtime->tanggal)->translatedFormat('d F Y') }}
                                </p>
                                
                                <p><i class="fa fa-clock-o text-danger me-2"></i>
                                    {{ \Carbon\Carbon::parse($showtime->jam)->format('H:i') }} WIB
                                </p>

                                {{-- Asumsi $warnaRuangan didefinisikan di controller/view composer --}}
                                <p><i class="fa fa-video-camera text-danger me-2"></i>
                                    <span class="{{ $warnaRuangan ?? 'text-light' }}">{{ $showtime->ruangan->nama }}</span>
                                </p>
                                
                                <p><i class="fa fa-money text-danger me-2"></i>
                                    Rp {{ number_format($showtime->harga, 0, ',', '.') }}
                                </p>
                                
                                <p><i class="fa fa-ticket text-danger me-2"></i>
                                Ticket Tersedia: <span id="available-seats-count">{{ $availableCount }}</span>
                                </p>
                                
                                <hr class="border-secondary">
                                
                                <h6 class="text-white">Ringkasan Pesanan</h6>
                                <p class="mb-1">Jumlah Tiket: <span id="total-tickets" class="fw-bold text-warning">0</span></p>
                                <p class="mb-0">Total Harga: <span class="fw-bold text-warning">Rp <span id="total-price-display">0</span></span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Kursi (Main Content) --}}
                    {{-- Di layar besar/menengah (lg/md) 8 kolom, di layar kecil (sm/xs) 12 kolom --}}
                    <div class="col-lg-8 col-md-6 col-12 mb-4 order-md-2 order-1">
                        <div class="seat-selection-section">
                            <div class="seat-legend">
                                <div><div class="seat available"></div> Tersedia</div>
                                <div><div class="seat booked"></div> Dipesan</div>
                                <div><div class="seat selected"></div> Dipilih</div>
                            </div>

                            <div class="screen-indicator">LAYAR</div>

                            <div class="seat-container">
                                {{-- 🚨 Tambahkan div ini untuk scrolling horizontal di mobile --}}
                                <div class="table-responsive-custom"> 
                                    <table class="seat-table">
                                        <tbody>
                                            @php
                                                // Maksimal 10 baris (A–J), atau sesuai konfigurasi Ruangan Anda
                                                $maxRows = 10;
                                                $maxCols = 10; // Maksimal 10 kolom (1–10) per baris
                                            @endphp
                                            @for ($row = 1; $row <= $maxRows; $row++)
                                                
                                                <tr>
                                                    <th>{{ chr(64 + $row) }}</th>
                                                    @for ($col = 1; $col <= $maxCols; $col++)
                                                        @php
                                                            $seatCode = chr(64 + $row) . $col;
                                                            // Asumsi kursi di controller sudah disiapkan per kode (A1, A2, dst.)
                                                            $seat = collect($seatsForJs)->firstWhere('nomor_kursi', $seatCode);
                                                            $seatNumber = (($row - 1) * $maxCols) + $col;

                                                            // Tampilkan kursi hanya jika nomor kursi masih dalam batas kapasitas ruangan
                                                            $isBeyondCapacity = $seatNumber > $showtime->ruangan->kapasitas;
                                                        @endphp
                                                        <td class="seat-wrapper">
                                                            @if(!$isBeyondCapacity)
                                                                <div class="seat {{ $seat['status'] ?? 'available' }}"
                                                                     data-seat-code="{{ $seatCode }}" {{-- Ganti data-seat-id ke data-seat-code --}}
                                                                     data-seat-status="{{ $seat['status'] ?? 'available' }}">
                                                                    {{ $col }}
                                                                </div>
                                                            @else
                                                                {{-- Kursi Kosong (Beyond Capacity) --}}
                                                                <div class="seat-placeholder"></div>
                                                            @endif
                                                        </td>
                                                    @endfor
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORM --}}
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <form action="{{ route('order.store') }}" method="POST" id="order-form">
                            @csrf
                            <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                            <div id="selected-seats-wrapper"></div>
                            <input type="hidden" id="total-price-input" name="total_price">

                            <div class="d-flex justify-content-center">
                                <button type="submit" id="submit-order-btn" class="site-btn" disabled>
                                    Pesan Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const seats = document.querySelectorAll('.seat');
    const selectedSeatsWrapper = document.getElementById('selected-seats-wrapper');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const totalPriceInput = document.getElementById('total-price-input');
    const totalTicketsDisplay = document.getElementById('total-tickets');
    const submitBtn = document.getElementById('submit-order-btn');

    let selectedSeats = [];

    function updateSummary() {
        const totalTickets = selectedSeats.length;
        const ticketPrice = {{ $showtime->harga }};
        const totalPrice = totalTickets * ticketPrice;

        totalTicketsDisplay.textContent = totalTickets;
        // Format harga ke mata uang Rupiah
        totalPriceDisplay.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
        totalPriceInput.value = totalPrice;

        // Update Hidden Inputs untuk form submission
        selectedSeatsWrapper.innerHTML = '';
        selectedSeats.forEach(seatCode => { // Menggunakan seatCode
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_seats[]';
            input.value = seatCode; // Kirim kode kursi (A1, B2, dst.)
            selectedSeatsWrapper.appendChild(input);
        });

        submitBtn.disabled = totalTickets === 0;
    }

    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const status = this.dataset.seatStatus;
            const seatCode = this.dataset.seatCode; // Ambil data-seat-code

            if (status === 'available' && seatCode) {
                if (this.classList.contains('selected')) {
                    // Deselect
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(code => code !== seatCode);
                } else {
                    // Select
                    this.classList.add('selected');
                    selectedSeats.push(seatCode);
                }
                updateSummary();
            }
        });
    });
});
</script>
    
<style>
/* 1. Responsivitas Tata Letak Kolom */
/* Pastikan di layar kecil, area pemilihan kursi (8 kolom) tampil di atas summary (4 kolom) */
@media (max-width: 767.98px) {
    /* order-1 untuk kolom kursi, order-2 untuk kolom summary */
    .order-1 { order: 1; }
    .order-2 { order: 2; }
}

/* 2. Style Card Summary */
.order-summary-card {
    border: 1px solid #e53637; /* Border merah agar lebih menonjol */
}
.order-summary-card p {
    color: #ccc;
    font-size: 0.95rem;
}
.order-summary-card .text-warning {
    color: #ffc107 !important; /* Pastikan warna warning tampil */
}

/* 3. Penyesuaian Tabel Kursi Responsif */
.table-responsive-custom {
    /* Mengaktifkan scroll horizontal di perangkat kecil */
    overflow-x: auto;
    padding-bottom: 10px; /* Ruang untuk scrollbar di bawah */
}
.seat-table { 
    border-collapse: collapse; 
    margin: 0 auto; 
    /* Minimal lebar tabel agar scroll berfungsi jika kursi terlalu banyak */
    min-width: 500px; 
}

/* 4. Ukuran Kursi dan Margin untuk Desktop (Default) */
.seat { 
    width: 40px; 
    height: 40px; 
    /* ... lainnya ... */
}
.seat-placeholder {
    width: 40px; 
    height: 40px; 
    background-color: transparent; 
}

/* 5. Media Query untuk Layar Kecil (Mobile/Tablet) */
@media (max-width: 576px) {
    /* Perkecil ukuran kursi untuk menghemat ruang di ponsel */
    .seat {
        width: 30px; 
        height: 30px;
        font-size: 10px;
    }
    .seat-placeholder {
        width: 30px; 
        height: 30px; 
    }
    /* Perkecil padding di tabel */
    .seat-table th, 
    .seat-table td { 
        padding: 1px;
    }
    .seat-legend {
        gap: 10px;
        font-size: 12px;
    }
    .seat-legend .seat { 
        width: 15px; 
        height: 15px; 
    }
    .seat-selection-section {
        padding: 15px; /* Kurangi padding section di mobile */
    }
}

/* --- CSS Sisanya --- */
.seat-selection-section {
    background-color: #0d0d0d;
    padding: 30px;
    border-radius: 8px;
    color: #fff;
}
.seat-legend { 
    display: flex; 
    gap: 20px; 
    justify-content: center; 
    margin-bottom: 20px; 
}
.seat-legend .seat { 
    width: 20px; 
    height: 20px; 
    border-radius: 4px; 
    display: inline-block; 
    margin-right: 5px; 
}
.seat-legend .available { background-color: #28a745; }
.seat-legend .booked { background-color: #dc3545; }
.seat-legend .selected { background-color: #007bff; }
.screen-indicator { 
    background-color: #555; 
    color: #eee; 
    text-align: center; 
    padding: 10px 0; 
    border-radius: 5px; 
    font-weight: bold; 
    margin-bottom: 30px; 
}
.seat-table th { 
    color: #aaa; 
    font-size: 12px; 
    font-weight: bold; 
}
.seat { 
    border-radius: 4px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    cursor: pointer; 
    transition: background-color 0.2s ease, transform 0.1s ease;
    font-weight: bold;
    color: #fff;
}
.seat.available { background-color: #28a745; }
.seat.booked { background-color: #dc3545; cursor: not-allowed; } 
.seat.selected { background-color: #007bff; }
.seat.available:hover {
    background-color: #218838;
    transform: scale(1.05);
}
</style>
    
@endpush