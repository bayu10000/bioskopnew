@extends('layouts.template')

@section('title', 'Pemesanan Tiket')

@section('content')
<section class="anime-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="section-title">
                    <h4>Pemesanan Tiket</h4>
                </div>
                <div class="row">
                    {{-- Detail Film & Showtime --}}
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 bg-dark text-white">
                            <div class="card-body">
                                <h5 class="card-title">{{ $showtime->film->judul }}</h5>
                                <hr class="border-secondary">
                                <p><i class="fa fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($showtime->tanggal)->translatedFormat('d F Y') }}
                                </p>
                                <p><i class="fa fa-clock-o"></i>
                                    {{ \Carbon\Carbon::parse($showtime->jam)->format('H:i') }}
                                </p>
                                <p><i></i>
                                    Rp {{ number_format($showtime->harga, 0, ',', '.') }}
                                </p>
                                <p><i class="fa fa-chair"></i>
                                Ticket Tersedia: <span id="available-seats-count">{{ $availableCount }}</span>
                                </p>
                                <hr class="border-secondary">
                                <h6>Ringkasan Pesanan</h6>
                                <p>Jumlah Tiket: <span id="total-tickets">0</span></p>
                                <p>Total Harga: Rp <span id="total-price-display">0</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Kursi --}}
                    <div class="col-lg-8 col-md-6 mb-4">
                        <div class="seat-selection-section">
                            <div class="seat-legend">
                                <div><div class="seat available"></div> Tersedia</div>
                                <div><div class="seat booked"></div> Dipesan</div>
                                <div><div class="seat selected"></div> Dipilih</div>
                            </div>

                            <div class="screen-indicator">LAYAR</div>

                            <div class="seat-container">
                                <table class="seat-table">
                                    <tbody>
                                        @php
                                            $seatRows = $showtime->ruangan->kapasitas / 10;
                                        @endphp
                                        @for ($row = 1; $row <= $seatRows; $row++)
                                            <tr>
                                                <th>{{ chr(64 + $row) }}</th>
                                                @for ($col = 1; $col <= 10; $col++)
                                                    @php
                                                        $seatCode = chr(64 + $row) . $col;
                                                        $seat = collect($seatsForJs)->firstWhere('nomor_kursi', $seatCode);
                                                    @endphp
                                                    <td class="seat-wrapper">
                                                        <div class="seat {{ $seat['status'] ?? 'available' }}"
                                                             data-seat-id="{{ $seat['id'] ?? '' }}"
                                                             data-seat-status="{{ $seat['status'] ?? 'available' }}">
                                                            {{ $col }}
                                                        </div>
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
        const totalPrice = totalTickets * {{ $showtime->harga }};

        totalTicketsDisplay.textContent = totalTickets;
        totalPriceDisplay.textContent = new Intl.NumberFormat('id-ID').format(totalPrice);
        totalPriceInput.value = totalPrice;

        selectedSeatsWrapper.innerHTML = '';
        selectedSeats.forEach(seatId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_seats[]';
            input.value = seatId;
            selectedSeatsWrapper.appendChild(input);
        });

        submitBtn.disabled = totalTickets === 0;
    }

    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const status = this.dataset.seatStatus;
            const seatId = this.dataset.seatId;

            if (status === 'available' && seatId) {
                if (this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(id => id !== seatId);
                } else {
                    this.classList.add('selected');
                    selectedSeats.push(seatId);
                }
                updateSummary();
            }
        });
    });
});
</script>
    
<style>
    /* Container area pemilihan kursi */
    .seat-selection-section {
        background-color: #0d0d0d;
        padding: 30px;
        border-radius: 8px;
        color: #fff;
    }
    
    /* Legend */
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
    
    /* Status kursi di legend */
    .seat-legend .available { background-color: #28a745; }
    .seat-legend .booked { background-color: #dc3545; }
    .seat-legend .selected { background-color: #007bff; }
    
    /* Indikator layar */
    .screen-indicator { 
        background-color: #555; 
        color: #eee; 
        text-align: center; 
        padding: 10px 0; 
        border-radius: 5px; 
        font-weight: bold; 
        margin-bottom: 30px; 
    }
    
    /* Table kursi */
    .seat-table { 
        border-collapse: collapse; 
        margin: 0 auto; 
    }
    .seat-table th, 
    .seat-table td { 
        padding: 4px; 
        text-align: center; 
    }
    .seat-table th { 
        color: #aaa; 
        font-size: 12px; 
        font-weight: bold; 
    }
    
    /* Kotak kursi */
    .seat { 
        width: 40px; 
        height: 40px; 
        border-radius: 4px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        cursor: pointer; 
        transition: background-color 0.2s ease, transform 0.1s ease;
        font-size: 14px; 
        font-weight: bold;
        color: #fff;
    }
    
    /* Warna default untuk kursi sesuai status */
    .seat.available { background-color: #28a745; }  /* hijau */
    .seat.booked { background-color: #dc3545; cursor: not-allowed; } /* merah */
    .seat.selected { background-color: #007bff; } /* biru */
    
    /* Efek hover hanya untuk kursi available */
    .seat.available:hover {
        background-color: #218838;
        transform: scale(1.05);
    }
    
    .seat-wrapper { 
        position: relative; 
    }
    </style>
    
@endpush