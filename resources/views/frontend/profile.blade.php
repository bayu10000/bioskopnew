@extends('layouts.template')

@section('content')

<section id="hero" class="hero section dark-background">
    <img src="{{ asset('img/back.png') }}" alt="Background" data-aos="fade-in">
    <div class="hero-gradient-overlay"></div>

    <div class="container d-flex flex-column align-items-center">
        <h2 data-aos="fade-up" data-aos-delay="100">SELAMAT DATANG DI CHINEPHILE!</h2>
        <p data-aos="fade-up" data-aos-delay="200">Lebih dari sekadar menonton, ini adalah pengalaman sinematik.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('home') }}" class="btn-get-started">LIHAT FILM</a>
        </div>
    </div>
</section>

<section class="anime-details spad" style="padding-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="section-title text-center mb-4">
                    <h4>Kenapa Memilih Cinephile?</h4>
                </div>
                <div class="showtime-card p-4 p-md-5 text-center">
                    <div class="row mt-4">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="inner-card p-4 rounded h-100">
                                <h5 class="text-danger mb-2">Kualitas Terbaik</h5>
                                <p class="text-white-50 mb-0">Rasakan setiap adegan dengan layar proyektor beresolusi tinggi dan sistem suara Dolby Atmos.</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="inner-card p-4 rounded h-100">
                                <h5 class="text-danger mb-2">Pilihan Film Terkurasi</h5>
                                <p class="text-white-50 mb-0">Dari blockbuster terbaru hingga film independen, kami menyajikan pilihan terbaik untuk Anda.</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="inner-card p-4 rounded h-100">
                                <h5 class="text-danger mb-2">Kenyamanan Maksimal</h5>
                                <p class="text-white-50 mb-0">Duduk santai di kursi ergonomis kami yang dirancang untuk kenyamanan menonton Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Styling Tambahan --}}
<style>
.hero.section {
    position: relative;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
}

.hero.section img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
}

.hero.section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 2;
}

.hero-gradient-overlay {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(to top, rgba(13, 13, 13, 1), rgba(13, 13, 13, 0));
    z-index: 3;
}

.hero.section .container {
    position: relative;
    z-index: 4;
    padding: 0 15px;
}

.hero.section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.hero.section p {
    font-size: 1.1rem;
    color: #fff;
    max-width: 600px;
    margin: 0 auto;
}

.btn-get-started {
    background: #e53637;
    color: #fff;
    padding: 10px 25px;
    border-radius: 5px;
    transition: 0.3s;
    text-transform: uppercase;
    font-weight: 600;
    text-decoration: none;
}

.btn-get-started:hover {
    background: #ff5252;
}

.showtime-card {
    background: #0d0d0d;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    color: #fff;
    transition: all 0.3s;
}

.showtime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

.showtime-card .inner-card {
    background: #1a1a1a;
    border: 1px solid #333;
}

/* 🔹 Responsive */
@media (max-width: 991px) {
    .hero.section h2 {
        font-size: 2rem;
    }
    .hero.section p {
        font-size: 1rem;
    }
}

@media (max-width: 767px) {
    .hero.section {
        min-height: 80vh;
        padding: 50px 0;
    }
    .hero.section h2 {
        font-size: 1.6rem;
    }
    .hero.section p {
        font-size: 0.95rem;
    }
    .btn-get-started {
        padding: 8px 18px;
        font-size: 0.9rem;
    }
}

@media (max-width: 575px) {
    .hero.section h2 {
        font-size: 1.4rem;
    }
    .hero.section p {
        font-size: 0.85rem;
    }
    .btn-get-started {
        width: 100%;
        text-align: center;
    }
}
</style>
@endsection
