@extends('layouts.template')

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
                <div class="col-lg-10 offset-lg-1">
                    <div class="section-title mb-4">
                        <h4>Daftar Pesanan Saya</h4>
                    </div>

                    @forelse($orders as $order)
                        <div class="showtime-card mb-4">
                            <div class="card-header border-bottom border-secondary p-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Pesanan</h5>
                                <span class="text-white-50">
                                    {{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y, H:i') }} WIB
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <div class="row">
                                    @foreach($order->seats as $seat)
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="inner-card p-3 rounded h-100 d-flex flex-column justify-content-between">
                                                <div class="mb-2">
                                                    <h6 class="text-white">Film: <span class="text-danger">{{ $order->showtime->film->judul ?? '-' }}</span></h6>
                                                    <p class="text-white-50 mb-1">Nomor Kursi: <span class="text-white">{{ $seat->nomor_kursi }}</span></p>
                                                    <p class="text-white-50 mb-1">Ruangan: <span class="text-white">{{ $order->showtime->ruangan->nama ?? '-' }}</span></p>
                                                    <p class="text-white-50 mb-1">Tanggal Tayang: <span class="text-white">{{ \Carbon\Carbon::parse($order->showtime->tanggal)->translatedFormat('d F Y') ?? '-' }}</span></p>
                                                    <p class="text-white-50 mb-0">Jam Tayang: <span class="text-white">{{ \Carbon\Carbon::parse($order->showtime->jam)->format('H:i') ?? '-' }}</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="card-footer border-top border-secondary p-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}</h5>
                                <span class="badge {{ $order->status === 'pending' ? 'bg-warning' : ($order->status === 'paid' ? 'bg-success' : 'bg-danger') }} text-white">
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

{{-- Styling Tambahan untuk tampilan yang lebih bagus --}}
<style>
.showtime-card {
    background: #0d0d0d;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: #fff;
}

.showtime-card .card-header,
.showtime-card .card-footer {
    background-color: transparent;
    border-color: #333 !important;
}

.showtime-card .inner-card {
    background: #1a1a1a;
    border: 1px solid #333;
}

.showtime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}
</style>
@endsection