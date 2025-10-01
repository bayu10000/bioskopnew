@extends('layouts.template')

@section('content')
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="{{ url('/') }}"><i class="fa fa-home"></i> Beranda</a>
                    <a href="{{ route('home') }}">Film</a> {{-- Tambahkan route('home') agar link "Film" bisa diklik --}}
                    <span>{{ $film->judul }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="anime-details spad">
    <div class="container">
        <div class="anime__details__content">
            <div class="row">
                {{-- Kolom Poster (Responsif: 12 di XS, 4 di SM, 3 di LG) --}}
                <div class="col-lg-3 col-md-4 col-sm-4 col-12 mb-4 mb-lg-0"> {{-- Diubah ke col-md-4 agar lebih proporsional --}}
                    <div class="anime__details__pic set-bg" data-setbg="{{ asset('storage/' . $film->poster) }}" style="background-image: url('{{ asset('storage/' . $film->poster) }}');">
                        <div class="ep">
                            @if($film->durasi)
                                {{ $film->durasi }} min
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="comment"><i class="fa fa-comments"></i> {{ $film->showtimes->count() }} Jadwal</div>
                        <div class="view"><i class="fa fa-eye"></i> {{ $film->views ?? 0 }}</div>
                    </div>
                </div>
                {{-- Kolom Detail (Responsif: 12 di XS, 8 di SM, 9 di LG) --}}
                <div class="col-lg-9 col-md-8 col-sm-8 col-12"> {{-- Diubah ke col-md-8 agar lebih proporsional --}}
                    <div class="anime__details__text">
                        <div class="anime__details__title">
                            <h3>{{ $film->judul }}</h3>
                            <span>{{ $film->sutradara }}</span>
                        </div>
                        <p>{{ $film->sinopsis }}</p>
                        <div class="anime__details__widget">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <ul>
                                        <li><span>Genre:</span>
                                            @foreach($film->genres as $genre)
                                                {{ $genre->nama }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </li>
                                        <li><span>Sutradara:</span> {{ $film->sutradara }}</li>
                                        <li><span>Aktor:</span> {{ $film->aktor }}</li>
                                    </ul>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <ul>
                                        <li><span>Tanggal Mulai Tayang:</span> {{ \Carbon\Carbon::parse($film->tanggal_mulai)->translatedFormat('d F Y') }}</li>
                                        <li><span>Tanggal Selesai Tayang:</span> {{ \Carbon\Carbon::parse($film->tanggal_selesai)->translatedFormat('d F Y') }}</li>
                                        <li><span>Harga Tiket:</span>
                                            @if($showtimes->isNotEmpty())
                                                Rp {{ number_format($showtimes->first()->harga, 0, ',', '.') }}
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- Tombol Lihat Trailer --}}
                        <div class="d-flex justify-content-start align-items-center mt-4">
                            @if($film->link_trailer)
                                <a href="{{ $film->link_trailer }}" class="watch-video-btn glightbox"><i class="fa fa-play"></i> Lihat Trailer</a>
                            @else
                                <span class="watch-video-btn-disabled"><i class="fa fa-play"></i> Trailer Tidak Tersedia</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Tayang Section --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="product__page__title mt-5">
                    <div class="section-title">
                        <h4>Jadwal Tayang</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Filter Tanggal --}}
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('film.show', $film->id) }}" method="GET" class="mb-4">
                    <div class="input-group">
                        {{-- nice-select akan diinisialisasi oleh JS --}}
                        <select name="date" class="form-control nice-select">
                            <option value="">-- Pilih Tanggal --</option>
                            @foreach($showtimeDates as $date)
                                <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger filter-btn-custom">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Jadwal Tayang Berdasarkan Tanggal --}}
        <div class="row">
            <div class="col-lg-12 text-black">
                @if ($showtimes->count() > 0)
                    <div class="product__page__content">
                        <div class="row">
                            @foreach ($showtimes as $showtime)
                                @php
                                    $warnaRuangan = match($showtime->ruangan->nama) {
                                        'Ruangan 1' => 'text-primary', // biru
                                        'Ruangan 2' => 'text-warning', // kuning
                                        'Ruangan 3' => 'text-success', // hijau
                                        default => 'text-light',
                                    };
                                @endphp
                                {{-- Grid Responsif untuk Kartu Jadwal: 12/XS, 6/SM, 4/MD, 3/LG --}}
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                                    <div class="showtime-card">
                                        <div class="showtime-time">
                                            <h6>{{ \Carbon\Carbon::parse($showtime->jam)->format('H:i') }} WIB</h6>
                                        </div>
                                        <div class="showtime-details">
                                            <p><strong>Studio:</strong> <span class="{{ $warnaRuangan }}">{{ $showtime->ruangan->nama }}</span></p>
                                            <p><strong>Harga:</strong> Rp {{ number_format($showtime->harga, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="showtime-action mt-auto">
                                            @auth
                                                <a href="{{ route('order.show', $showtime->id) }}" class="btn primary-btn w-100">Beli Tiket</a>
                                            @else
                                                <a href="{{ route('login.form') }}" class="btn primary-btn w-100">Login untuk Beli</a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif ($selectedDate)
                    <div class="alert alert-info text-center" role="alert">
                        Tidak ada jadwal tayang untuk tanggal ini.
                    </div>
                @endif
            </div>
        </div>

        {{-- Catatan: Bagian .seat-selection-section di bawah ini (kursi) tidak memiliki data yang di-loop. 
             Jika Anda menggunakannya, pastikan data kursi dimuat di controller. --}}
        
    </div>
</section>

{{-- Styling Tambahan --}}
<style>
/* 3. Style Poster Film - Disesuaikan dengan Rasio 2:3 */
.anime__details__pic {
    /* Tambahkan properti untuk membuat poster responsif pada semua ukuran */
    width: 100%;
    /* Menggunakan rasio 2:3 (3/2 * 100% = 150%) */
    padding-top: 150%; 
    background-size: cover;
    background-position: center;
    position: relative;
}

/* 1. Responsif Input Group Filter */
.input-group {
    display: flex; /* Memastikan select dan button berada dalam satu baris */
}
.input-group .nice-select.form-control {
    flex-grow: 1; /* Select akan mengambil sisa ruang */
    border-radius: 5px 0 0 5px !important; /* Memperbaiki nice-select radius */
}
.input-group-append .filter-btn-custom {
    border-radius: 0 5px 5px 0 !important;
    background-color: #e53637;
    height: 50px;
    padding: 0 20px;
    line-height: 50px;
}
/* Memperbaiki nice-select agar sesuai dengan tinggi tombol */
.nice-select.form-control {
    /* ... kode yang sudah ada ... */
    height: 50px;
    line-height: 50px;
    /* ... kode yang sudah ada ... */
}
/* Nice-select untuk layar kecil agar tidak terpotong */
@media (max-width: 575.98px) {
    .input-group {
        flex-direction: column; /* Select dan Button menjadi tumpukan di layar sangat kecil */
    }
    .input-group .nice-select.form-control {
        border-radius: 5px !important;
        margin-bottom: 10px;
    }
    .input-group-append {
        width: 100%;
    }
    .input-group-append .filter-btn-custom {
        width: 100%;
        border-radius: 5px !important;
    }
}


/* 2. Style Kartu Jadwal */
.showtime-card {
    /* ... kode yang sudah ada ... */
    /* Pastikan gambar poster responsif */
    max-width: 100%;
    height: auto;
}
/* Memperbaiki alignment text-black di kolom Jadwal Tayang */
.col-lg-12.text-black p {
    color: #fff; /* Mengganti warna teks detail di kartu jadwal menjadi putih */
}
.col-lg-12.text-black strong {
    color: #aaa; /* Memberi warna berbeda pada label strong */
}


/* 4. Style Kursi Bioskop (Membuat tabel kursi lebih *scrollable* di layar kecil) */
@media (max-width: 767.98px) {
    .seat-selection-section {
        overflow-x: auto; /* Memungkinkan scroll horizontal jika tabel kursi terlalu lebar */
        padding: 15px;
    }
    .seat-table {
        min-width: 400px; /* Minimal lebar tabel untuk mencegah terlalu sempit */
    }
    .seat {
        width: 30px; /* Perkecil ukuran kursi */
        height: 30px;
        font-size: 12px;
    }
    .seat-table th {
        padding: 2px;
    }
}


/* -- Hapus atau pertahankan CSS Anda yang lain di bawah ini, sudah cukup baik -- */

.showtime-card {
    background: #0d0d0d;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    height: 100%;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.showtime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

.showtime-time h6 {
    color: #e53637;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.showtime-details p {
    margin: 5px 0;
    font-size: 14px;
}

.watch-video-btn {
    text-decoration: none;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}

.watch-video-btn i {
    font-size: 2rem;
    line-height: 0;
    margin-right: 8px;
    color: #e53637;
    transition: 0.3s;
}

.watch-video-btn:hover i {
    color: #ff5252;
}

.watch-video-btn-disabled {
    text-decoration: none;
    color: #888;
    font-size: 1.1rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    cursor: not-allowed;
}

.watch-video-btn-disabled i {
    font-size: 2rem;
    line-height: 0;
    margin-right: 8px;
    color: #888;
}

.nice-select .list li {
    color: #000;
}

/* Bagian untuk kursi bioskop */
.seat-selection-section {
    background-color: #0d0d0d;
    padding: 30px;
    border-radius: 8px;
    color: #fff;
}
.seat-legend { display: flex; gap: 20px; justify-content: center; margin-bottom: 20px; }
.seat-legend .seat { width: 20px; height: 20px; border-radius: 4px; display: inline-block; margin-right: 5px; }
.screen-indicator { background-color: #555; color: #eee; text-align: center; padding: 10px 0; border-radius: 5px; font-weight: bold; margin-bottom: 30px; }
.seat-table { border-collapse: collapse; margin: 0 auto; }
.seat-table th, .seat-table td { padding: 4px; text-align: center; }
.seat-table th { color: #aaa; font-size: 12px; font-weight: bold; }
.seat-wrapper { position: relative; }
.seat {
    width: 40px;
    height: 40px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s ease;
    font-size: 14px;
    color: white; 
}
.seat.available, .seat-legend .available { background-color: #28a745; }
.seat.booked, .seat-legend .booked { background-color: #dc3545; cursor: not-allowed; }
.seat.selected, .seat-legend .selected { background-color: #007bff; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi GLightbox
        const glightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true,
            videosWidth: '900px',
            videosHeight: '500px'
        });
        
        // Inisialisasi Nice-Select (Jika Anda menggunakan plugin nice-select)
        // Jika nice-select tidak otomatis terinisialisasi, tambahkan ini:
        // $('.nice-select').niceSelect(); 
    });
</script>
@endpush

@push('head_scripts')
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
@endpush