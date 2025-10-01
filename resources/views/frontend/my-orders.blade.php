@extends('layouts.template')

@section('title', 'Daftar Pesanan Saya') {{-- Pastikan judul section ada --}}

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
                {{-- Gunakan col-12 di sini untuk responsivitas penuh --}}
                <div class="col-lg-10 offset-lg-1 col-12"> 
                    <div class="section-title mb-4">
                        <h4>Daftar Pesanan Saya</h4>
                    </div>

                    @forelse($orders as $order)
                        <div class="showtime-card mb-4" id="order-card-{{ $order->id }}">
                            {{-- Header --}}
                            <div class="card-header border-bottom border-secondary p-3 d-flex flex-wrap justify-content-between align-items-center position-relative">
                                
                                {{-- Tombol Print dipindah ke kiri bawah header agar tidak terlalu menonjol --}}
                                @if($order->status === 'paid')
                                    <button class="btn btn-sm btn-danger print-btn-pos" onclick="printCard('order-card-{{ $order->id }}')">
                                        <i class="fa fa-print"></i> Print Tiket
                                    </button>
                                @else
                                    {{-- Placeholder agar konten lain sejajar --}}
                                    <div class="print-btn-pos-placeholder"></div> 
                                @endif
                                
                                <h5 class="mb-0 mx-auto mx-md-0 order-1 order-md-2">Pesanan</h5>
                                
                                {{-- Gunakan order-3 di mobile agar tanggal di kanan bawah --}}
                                <span class="text-white-50 ms-auto ms-md-0 order-3 order-md-3">
                                    {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            </div>

                            {{-- Body --}}
                            <div class="card-body p-4">
                                {{-- Gunakan flex-column di mobile, biarkan default di desktop --}}
                                <div class="inner-card p-3 rounded h-100 d-flex flex-column justify-content-between">
                                    <div class="mb-2 text-start">
                                        @if($order->user)
                                            <h6 class="text-white mb-2">
                                                Pelanggan: <span class="text-danger">{{ $order->user->name }}</span>
                                            </h6>
                                        @endif

                                        <h6 class="text-white mb-2">
                                            Film: <span class="text-danger">{{ $order->showtime->film->judul ?? '-' }}</span>
                                        </h6>
                                        
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <p class="text-white-50 mb-1">Nomor Kursi: <span class="text-white">{{ $order->seats->pluck('nomor_kursi')->implode(', ') }}</span></p>
                                                <p class="text-white-50 mb-1">Ruangan: <span class="text-white">{{ $order->showtime->ruangan->nama ?? '-' }}</span></p>
                                            </div>
                                            <div class="col-sm-6">
                                                <p class="text-white-50 mb-1">Tanggal Tayang: <span class="text-white">{{ \Carbon\Carbon::parse($order->showtime->tanggal)->translatedFormat('d F Y') ?? '-' }}</span></p>
                                                <p class="text-white-50 mb-0">Jam Tayang: <span class="text-white">{{ \Carbon\Carbon::parse($order->showtime->jam)->format('H:i') ?? '-' }} WIB</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="card-footer border-top border-secondary p-3 d-flex flex-wrap justify-content-between align-items-center">
                                <h5 class="mb-0 text-white order-2 order-md-1">
                                    Total: Rp <span class="text-danger">{{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </h5>
                                {{-- Memberikan margin di mobile --}}
                                <span class="badge {{ $order->status === 'pending' ? 'bg-warning' : ($order->status === 'paid' ? 'bg-success' : 'bg-danger') }} text-white mb-2 mb-md-0 order-1 order-md-2">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center" role="alert">
                            Anda belum memiliki pesanan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Script untuk print card (Dipertahankan dan Disempurnakan) --}}
<script>
    function printCard(cardId) {
        let cardElement = document.getElementById(cardId);
        let printBtn = cardElement.querySelector('.print-btn-pos');
        let originalParent = printBtn ? printBtn.parentElement : null;

        // Sembunyikan tombol print saat dialog cetak muncul
        if (printBtn) {
            printBtn.style.display = 'none';
        }

        // Simpan konten asli
        let originalContent = document.body.innerHTML;
        
        // Ambil hanya HTML dari kartu pesanan yang ingin dicetak
        let cardContent = cardElement.innerHTML;

        // Buat konten baru untuk dicetak
        document.body.innerHTML = `
            <html>
            <head>
                <title>Cetak Tiket</title>
                <style>
                    /* Reset dan Font Dasar */
                    body { font-family: Arial, sans-serif; background: #fff; color: #000; padding: 20px; }
                    /* Style untuk Kotak Tiket (Menggantikan .showtime-card) */
                    .ticket-box { 
                        border: 2px solid #333; 
                        padding: 20px; 
                        border-radius: 8px;
                        color: #000;
                        background: #fff;
                        max-width: 600px;
                        margin: 0 auto;
                        box-shadow: none;
                        transition: none;
                    }
                    /* Styling Elemen Card */
                    .card-header, .card-footer {
                        border-color: #ccc !important;
                        background-color: #f8f9fa !important;
                        padding: 10px 0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        border-bottom: 1px solid #ccc;
                        border-top: 1px solid #ccc;
                    }
                    .card-header h5, .card-footer h5 { font-size: 1.2em; }
                    .card-body { padding: 20px 0; }
                    .inner-card {
                        background: #eee !important;
                        border: 1px solid #ccc !important;
                        padding: 15px !important;
                        border-radius: 6px;
                    }

                    /* Koreksi Warna untuk Cetak */
                    .text-white, .text-danger { color: #000 !important; }
                    .text-white-50 { color: #555 !important; }
                    .badge {
                        color: #fff !important;
                        padding: 5px 10px;
                        border-radius: 4px;
                        font-weight: bold;
                    }
                    .bg-success { background-color: green !important; }
                    .print-btn-pos { display: none !important; } /* Sembunyikan tombol print */
                    .print-btn-pos-placeholder { display: none !important; }
                    .order-1, .order-2, .order-3, .mx-auto { order: initial !important; margin: initial !important; }
                    
                    /* Utility classes for print */
                    .d-flex { display: flex; }
                    .justify-content-between { justify-content: space-between; }
                    .align-items-center { align-items: center; }
                    .mb-0 { margin-bottom: 0; }
                    .mb-2 { margin-bottom: 10px; }
                    .p-3 { padding: 15px; }
                    .p-4 { padding: 20px; }
                    .text-start { text-align: left; }
                </style>
            </head>
            <body>
                <div class="ticket-box">
                    ${cardContent}
                </div>
            </body>
            </html>
        `;

        window.print();
        
        // Kembalikan halaman ke keadaan semula
        document.body.innerHTML = originalContent;
        
        // Muat ulang (reload) halaman agar seluruh event listener dan DOM kembali seperti semula
        window.location.reload();
    }
</script>

{{-- Styling Tambahan --}}
<style>
.showtime-card {
    background: #0d0d0d;
    border-radius: 8px;
    /* Hilangkan padding luar, biarkan padding di card-body */
    text-align: center;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: #fff;
    border: 1px solid #333; /* Tambahkan border tipis */
}
.showtime-card .card-header,
.showtime-card .card-footer {
    background-color: transparent;
    border-color: #333 !important;
}
.showtime-card .inner-card {
    background: #1a1a1a;
    border: 1px solid #333;
    text-align: left; /* Teks di dalam inner-card rata kiri */
}
.showtime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

/* Penyesuaian Tombol Print */
.print-btn-pos {
    /* Atur posisi di kiri bawah header */
    position: absolute;
    left: 10px;
    bottom: 5px;
}
/* Placeholder agar elemen lain sejajar di header */
.print-btn-pos-placeholder {
    width: 90px; /* Lebar yang kira-kira sama dengan tombol print */
    height: 30px;
}

/* Responsivitas Footer & Header di Mobile */
@media (max-width: 576px) {
    .showtime-card .card-header,
    .showtime-card .card-footer {
        flex-direction: column; /* Tumpuk di mobile */
        align-items: flex-start; /* Rata kiri */
    }
    .showtime-card .card-header h5 {
        order: 2; /* Pindah judul ke tengah */
        margin: 5px 0;
    }
    .showtime-card .card-header span {
        order: 3; /* Pindah tanggal ke bawah */
        margin-top: 5px;
        font-size: 0.8rem;
    }
    .print-btn-pos {
        order: 1; /* Pindah tombol print ke atas */
        position: static;
        margin-bottom: 10px;
    }
    .print-btn-pos-placeholder {
        display: none !important;
    }
    .showtime-card .card-footer h5 {
        order: 2;
        margin-top: 10px;
    }
    .showtime-card .card-footer .badge {
        order: 1;
        margin-bottom: 10px;
    }
    .showtime-card .inner-card .row .col-sm-6 {
        /* Memastikan detail tayang bertumpuk di ponsel */
        flex: 0 0 100%; 
        max-width: 100%;
    }
}
</style>
@endsection