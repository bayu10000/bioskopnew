@extends('layouts.template')

@section('title', 'Daftar Pesanan Saya')

@section('content')
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="{{ url('/') }}"><i class="fa fa-home"></i> Beranda</a>
                    <a href="{{ route('my-orders') }}">Pesanan</a>
                    <span>Daftar Pesanan Saya</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="anime-details spad">
    <div class="container">
        <div class="anime__details__content">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 col-12"> 
                    <div class="section-title mb-4">
                        <h4>Daftar Pesanan Saya</h4>
                    </div>

                    {{-- Notifikasi --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif


                    @forelse($orders as $order)
                        <div class="showtime-card mb-4" id="order-card-{{ $order->id }}">
                            
                            {{-- Header Card: Tombol Lihat Tiket (Kiri) dan Waktu Pemesanan (Kanan) --}}
                            <div class="card-header border-bottom border-secondary p-3 d-flex justify-content-between align-items-center position-relative">
                                
                                @if($order->status === 'paid' && !empty($order->qr_code_hash))
                                    {{-- Jika sudah lunas dan ada hash QR, tampilkan tombol lihat tiket --}}
                                    <a href="{{ route('ticket.view', ['hash' => $order->qr_code_hash]) }}" target="_blank" class="btn btn-sm btn-danger print-btn-pos">
                                        <i class="fa fa-print"></i> Download e-ticket
                                    </a>
                                @else
                                    {{-- Jika belum lunas/status lain, tampilkan ID Pesanan --}}
                                    <h5 class="mb-0 text-white order-id-display">
                                        Pesanan {{ $order->id }}
                                    </h5>
                                @endif
                                
                                <span class="text-secondary small">
                                    Dipesan pada: {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y, H:i') }}
                                </span>
                            </div>

                            {{-- Body Card: Detail Kiri dan QR/Aksi Kanan --}}
                            <div class="card-body p-3 text-white">
                                <div class="row">
                                    
                                    {{-- Kolom Kiri (Detail) - col-md-8 --}}
                                    <div class="col-12 col-md-8 order-detail-left">
                                        <p><strong>Film:</strong> <span class="text-danger">{{ $order->showtime->film->judul }}</span></p>
                                        <p><strong>Pelanggan:</strong> {{ $order->user->name }}</p>
                                        <p><strong>Username:</strong> {{ $order->user->username }}</p>
                                        <p><strong>Jadwal Tayang:</strong> 
                                            {{ \Carbon\Carbon::parse($order->showtime->tanggal . ' ' . $order->showtime->jam)->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
                                        </p>
                                        <p><strong>Studio:</strong> {{ $order->showtime->ruangan->nama }}</p>
                                        <p><strong>Total Tiket:</strong> {{ $order->jumlah_tiket }}</p>
                                        <p>
                                            <strong>Kursi:</strong> 
                                            {{-- Menampilkan daftar kursi --}}
                                            @forelse($order->seats as $seat)
                                                @if ($order->status == 'cancelled')
                                                    {{-- Kursi yang dibatalkan ditampilkan dengan strikethrough dan badge abu-abu gelap --}}
                                                    <span class="badge bg-dark me-1 text-decoration-line-through" title="Kursi Dibatalkan">{{ $seat->nomor_kursi }} (BATAL)</span>
                                                @else
                                                    <span class="badge bg-secondary me-1">{{ $seat->nomor_kursi }}</span>
                                                @endif
                                            @empty
                                                {{-- Jika tidak ada kursi (misalnya error atau pembatalan yang sangat lama), tampilkan info ini --}}
                                                @if($order->status !== 'cancelled')
                                                    <span class="text-warning">Detail kursi tidak tersedia.</span>
                                                @endif
                                            @endforelse
                                        </p>
                                        <h4 class="mt-3">
                                            <strong>Total Harga:</strong> <span class="text-danger">Rp. {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                        </h4>
                                    </div>

                                    {{-- Kolom Kanan (QR, Status, dan Tombol Aksi) - col-md-4 --}}
                                    {{-- Kolom Kanan (QR, Status, dan Tombol Aksi) - col-md-4 --}}
                                   

                                    {{-- Kolom Kanan (QR, Status, dan Tombol Aksi) - col-md-4 --}}
                                    <div class="col-12 col-md-4 text-center qr-action-area">
                                    
                                        @php
                                            $showtimeDateTime = \Illuminate\Support\Carbon::parse($order->showtime->tanggal . ' ' . $order->showtime->jam);
                                            $currentDateTime = \Illuminate\Support\Carbon::now();
                                    
                                            $isPast = $showtimeDateTime->lessThan($currentDateTime);
                                            $isFuture = $showtimeDateTime->isFuture();
                                            
                                            // 1. Logic untuk tombol Batalkan (Hanya PENDING & Belum Tayang)
                                            $canCancel = ($order->status === 'pending') && $isFuture;
                                    
                                            // 2. Logic untuk tombol Konfirmasi Selesai (Hanya PAID & Sudah Lewat)
                                            $canMarkAsDone = ($order->status === 'paid') && $isPast;
                                        @endphp
                                    
                                        {{-- Container untuk QR dan Aksi (dipusatkan) --}}
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            
                                            {{-- QR CODE (Pusat Kanan) --}}
                                            @if($order->status == 'paid' && !empty($order->qr_code_hash))
                                                <div class="mb-2 d-inline-block p-2 bg-white rounded shadow-sm">
                                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate(route('ticket.view', $order->qr_code_hash)) !!}
                                                </div>
                                                <p class="mb-3"><a></a></p>
                                            @endif
                                            
                                            <p></p>
                                    
                                            {{-- 💡 TOMBOL KONFIRMASI SUDAH MENONTON (BARU) --}}
                                            @if($canMarkAsDone)
                                                <form action="{{ route('order.mark-as-done', $order->id) }}" method="POST" style="display: block; width: 100%; max-width: 200px;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                                        <i class="fa fa-check-circle"></i> Konfirmasi Sudah Menonton
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            {{-- TOMBOL CANCEL (hanya pending dan belum tayang) --}}
                                            @if($canCancel)
                                                <form action="{{ route('order.cancel', $order->id) }}" method="POST" style="display: block; width: 100%; max-width: 200px;" 
                                                    onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan #{{ $order->id }}? Kursi akan dilepas dan tersedia kembali.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                                        <i class="fa fa-times-circle"></i> Batalkan Pesanan
                                                    </button>
                                                </form>
                                            @endif
                                    
                                            {{-- Status (Presisi di tengah) --}}
                                            <p class="small text-secondary mt-3">
                                    
                                                @if($order->status == 'done')
                                                    {{-- 💡 STATUS DONE (BARU) --}}
                                                    <span class="badge bg-primary badge-xl">SELESAI DITONTON</span>
                                                @elseif($order->status == 'paid')
                                                    <span class="badge bg-success badge-xl">LUNAS</span>
                                                @elseif($order->status == 'pending')
                                                    <span class="badge bg-warning text-dark badge-xl">MENUNGGU KONFIRMASI</span>
                                                @elseif($order->status == 'cancelled') 
                                                    <span class="badge bg-danger badge-xl">DIBATALKAN</span>
                                                @else
                                                    <span class="badge bg-secondary badge-xl">{{ strtoupper($order->status) }}</span>
                                                @endif
                                            </p>
                                    
                                        </div>
                                    
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            Anda belum memiliki pesanan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

{{-- --- CSS Kustom --- --}}
@push('head_scripts')
<style>
/* CSS khusus untuk card pesanan */
.showtime-card {
    background-color: #1a1a1a;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    overflow: hidden;
}
.showtime-card .card-header {
    background-color: #0d0d0d;
    color: #fff;
    border-bottom: 1px solid #333;
}
.showtime-card .card-body {
    background-color: #1a1a1a;
    color: #fff;
}

/* 💡 Style untuk memperbesar badge */
.badge-xl {
    font-size: 1.1em;
    padding: .4em .8em;
}

/* Garis pemisah vertikal antara detail dan QR/Aksi (Hanya di Desktop) */
.qr-action-area {
    border-left: 1px solid #333;
    padding-left: 20px;
}
/* Memastikan semua item di kolom kanan terpusat */
.qr-action-area .d-flex.flex-column {
    width: 100%; 
    align-items: center !important; 
}


/* Penyesuaian Tombol Lihat/Print Tiket di Header (Posisi Kiri) */
/* class ini digunakan bersama dengan d-flex justify-content-between untuk memposisikan di kiri */
.print-btn-pos {
    position: static;
    margin-right: auto;
}

/* Responsivitas Mobile */
@media (max-width: 767.98px) {
    /* Hilangkan garis pemisah di mobile */
    .qr-action-area {
        border-left: none; 
        padding-left: 0;
        margin-top: 20px;
        text-align: center !important;
    }
    
    /* Tombol dan QR rata tengah di mobile */
    .qr-action-area .d-flex.flex-column {
        align-items: center !important;
    }
    .qr-action-area form, 
    .qr-action-area a.btn {
        /* Membatasi lebar tombol di mobile */
        max-width: 250px !important; 
        width: 100%;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* Pastikan header tetap rapi di mobile */
    .showtime-card .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    /* Menggeser ID order/Tombol Print ke atas atau bawah sesuai kebutuhan di mobile */
    .order-id-display,
    .print-btn-pos {
        order: 1;
        margin-right: 0 !important; /* Hilangkan margin auto yang di-set di desktop */
    }
    /* Pindahkan tanggal/waktu ke bagian bawah header */
    .showtime-card .card-header span {
        order: 2; 
        margin-top: 5px;
        font-size: 0.8rem;
    }
}
</style>
@endpush