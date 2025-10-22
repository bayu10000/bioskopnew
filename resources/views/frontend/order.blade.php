@extends('layouts.template')

@section('title', 'Pemesanan Tiket')

@section('content')
    <!-- =======================
         BREADCRUMB SECTION
    ======================== -->
    <div class="breadcrumb-option py-3">
        <div class="container">
            <div class="breadcrumb__links d-flex align-items-center gap-2">
                <a href="{{ route('profile') }}" class="text-decoration-none text-white fw-bold">
                    <i class="fa fa-home text-danger me-1"></i> Beranda
                </a>
                <span class="text-white"></span>
    
                <a href="{{ route('home') }}" class="text-decoration-none text-white fw-bold">
                    Film
                </a>
                <span class="text-white"></span>
    
                <a href="{{ route('film.show', $showtime->film->id) }}" class="text-decoration-none text-white fw-bold">
                    {{ $showtime->film->judul }}
                </a>
                <span class="text-white"></span>
    
                <span class="text-light" style="opacity: 0.6;">
                    Pemesanan Tiket
                </span>
                
            </div>
        </div>
    </div>
    

    <!-- =======================
         ORDER CONTENT SECTION
    ======================== -->
    <section class="anime-details spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 col-12">
                    <div class="section-title">
                        <h4>Pemesanan Tiket</h4>
                    </div>

                    <div class="row">
                        <!-- =======================
                             KARTU RINGKASAN FILM
                        ======================== -->
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

                                    <p><i class="fa fa-video-camera text-danger me-2"></i>
                                        <span class="{{ $warnaRuangan ?? 'text-light' }}">
                                            {{ $showtime->ruangan->nama }}
                                        </span>
                                    </p>

                                    <p><i class="fa fa-money text-danger me-2"></i>
                                        Rp {{ number_format($showtime->harga, 0, ',', '.') }}
                                    </p>

                                    <p><i class="fa fa-ticket text-danger me-2"></i>
                                        Tiket Tersedia: <span id="available-seats-count">{{ $availableCount }}</span>
                                    </p>

                                    <hr class="border-secondary">

                                    <h6 class="text-white">Ringkasan Pesanan</h6>
                                    <p class="mb-1">Jumlah Tiket:
                                        <span id="total-tickets" class="fw-bold text-warning">0</span>
                                    </p>
                                    <p class="mb-0">Total Harga:
                                        <span class="fw-bold text-warning">Rp <span id="total-price-display">0</span></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- =======================
                             PEMILIHAN KURSI
                        ======================== -->
                        <div class="col-lg-8 col-md-6 col-12 mb-4 order-md-2 order-1">
                            <div class="seat-selection-section">
                                <div class="seat-legend">
                                    <div><div class="seat available"></div> Tersedia</div>
                                    <div><div class="seat booked"></div> Dipesan</div>
                                    <div><div class="seat selected"></div> Dipilih</div>
                                </div>

                                <div class="screen-indicator">LAYAR</div>

                                <div class="seat-container">
                                    <div class="table-responsive-custom">
                                        <table class="seat-table">
                                            <tbody>
                                                @php
                                                    $maxRows = 10;
                                                    $maxCols = 10;
                                                @endphp
                                                @for ($row = 1; $row <= $maxRows; $row++)
                                                    <tr>
                                                        <th>{{ chr(64 + $row) }}</th>
                                                        @for ($col = 1; $col <= $maxCols; $col++)
                                                            @php
                                                                $seatCode = chr(64 + $row) . $col;
                                                                $seat = collect($seatsForJs)->firstWhere('nomor_kursi', $seatCode);
                                                                $seatNumber = (($row - 1) * $maxCols) + $col;
                                                                $isBeyondCapacity = $seatNumber > $showtime->ruangan->kapasitas;
                                                            @endphp
                                                            <td class="seat-wrapper">
                                                                @if(!$isBeyondCapacity)
                                                                    <div class="seat {{ $seat['status'] ?? 'available' }}"
                                                                         data-seat-code="{{ $seatCode }}"
                                                                         data-seat-status="{{ $seat['status'] ?? 'available' }}">
                                                                        {{ $col }}
                                                                    </div>
                                                                @else
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

                    <!-- =======================
                         FORM PEMESANAN
                    ======================== -->
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <form action="{{ route('order.store') }}" method="POST" id="order-form">
                                @csrf
                                <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                                <div id="selected-seats-wrapper"></div>
                                <input type="hidden" id="total-price-input" name="total_price">

                                <div class="d-flex justify-content-center gap-3 mt-3">
                                   
                                    <button type="submit" id="submit-order-btn" class="site-btn" disabled>
                                        <i class="fa fa-ticket-alt me-1"></i> Pesan Sekarang
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
        totalPriceDisplay.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
        totalPriceInput.value = totalPrice;

        selectedSeatsWrapper.innerHTML = '';
        selectedSeats.forEach(seatCode => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_seats[]';
            input.value = seatCode;
            selectedSeatsWrapper.appendChild(input);
        });

        submitBtn.disabled = totalTickets === 0;
    }

    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const status = this.dataset.seatStatus;
            const seatCode = this.dataset.seatCode;

            if (status === 'available' && seatCode) {
                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(code => code !== seatCode);
                } else {
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
/* ====== Responsivitas ====== */
@media (max-width: 767.98px) {
    .order-1 { order: 1; }
    .order-2 { order: 2; }
}

/* ====== Card Ringkasan ====== */
.order-summary-card {
    border: 1px solid #e53637;
}
.order-summary-card p {
    color: #ccc;
    font-size: 0.95rem;
}
.order-summary-card .text-warning {
    color: #ffc107 !important;
}

/* ====== Kursi & Layout ====== */
.table-responsive-custom {
    overflow-x: auto;
    padding-bottom: 10px;
}
.seat-table {
    border-collapse: collapse;
    margin: 0 auto;
    min-width: 500px;
}
.seat, .seat-placeholder {
    width: 40px;
    height: 40px;
}
@media (max-width: 576px) {
    .seat, .seat-placeholder {
        width: 30px;
        height: 30px;
        font-size: 10px;
    }
    .seat-table th, .seat-table td {
        padding: 1px;
    }
    .seat-legend {
        gap: 10px;
        font-size: 12px;
    }
}

/* ====== Warna & Interaksi ====== */
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

/* ====== Breadcrumb Responsif ====== */
.breadcrumb-option {
    background-color: transparent;
}

.breadcrumb__links {
    flex-wrap: wrap; /* biar bisa turun ke baris berikutnya */
    font-size: 0.9rem;
    line-height: 1.6;
}

.breadcrumb__links a {
    display: flex;
    align-items: center;
    color: #fff;
    font-weight: 600;
    white-space: nowrap;
}

.breadcrumb__links i {
    margin-right: 5px;
    color: #e53637;
}

.breadcrumb__links span {
    margin: 0 6px;
    color: #aaa;
}

/* Mobile optimization */
@media (max-width: 576px) {
    .breadcrumb__links {
        justify-content: flex-start;
        gap: 4px;
        font-size: 0.8rem;
    }

    .breadcrumb__links a {
        flex: 0 0 auto;
    }

    .breadcrumb-option {
        padding-left: 10px;
        padding-right: 10px;
    }
}

</style>
@endpush
