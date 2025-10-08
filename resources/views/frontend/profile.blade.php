@extends('layouts.template')

@section('content')

{{-- 1. HERO SECTION --}}
<section id="hero" class="hero section dark-background">
    {{-- Wrapper background slideshow --}}
    <div id="hero-slideshow">
        <div class="hero-slide active" style="background-image: url('{{ asset('img/cun.jpg') }}');"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('img/film.jpg') }}');"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('img/sup.jpg') }}');"></div>
        <div class="hero-slide" style="background-image: url('{{ asset('img/god.jpg') }}');"></div>
    </div>

    {{-- Overlay --}}
    <div class="hero-gradient-overlay"></div>

    {{-- <div class="container d-flex flex-column align-items-center justify-content-center">
        <h2 data-aos="fade-up" data-aos-delay="100">SELAMAT DATANG DI CINEPHILE!</h2>
        <p data-aos="fade-up" data-aos-delay="200">Lebih dari sekadar menonton, ini adalah pengalaman sinematik.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('home') }}" class="btn-get-started">LIHAT FILM</a>
        </div>
    </div> --}}
</section>
{{-- END HERO SECTION --}}

---

{{-- 2. KENAPA MEMILIH CINEPHILE --}}
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

---

{{-- 3. SCRIPT SLIDESHOW --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const slides = document.querySelectorAll(".hero-slide");
        let current = 0;

        function nextSlide() {
            slides[current].classList.remove("active");
            current = (current + 1) % slides.length;
            slides[current].classList.add("active");
        }

        setInterval(nextSlide, 5000); // ganti gambar setiap 5 detik
    });
</script>

---

{{-- 4. STYLE --}}
<style>
/* ==== HERO SECTION ==== */
.hero.section {
    position: relative;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
    background: #000;
}

/* Background slideshow wrapper */
#hero-slideshow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    overflow: hidden;
}

/* Tiap gambar slide */
.hero-slide {
    position: absolute;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1.2s ease-in-out;
}

.hero-slide.active {
    opacity: 1;
}

/* Overlay gradasi */
.hero-gradient-overlay {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(to top, rgba(13, 13, 13, 1), rgba(13, 13, 13, 0));
    z-index: 2;
}

/* Konten di atas slide */
.hero.section .container {
    position: relative;
    z-index: 3;
    padding: 0 15px;
}

.hero.section h2 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
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
    padding: 12px 28px;
    border-radius: 5px;
    transition: 0.3s;
    text-transform: uppercase;
    font-weight: 600;
    text-decoration: none;
}

.btn-get-started:hover {
    background: #ff5252;
}

/* ==== CARD SECTION ==== */
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

.inner-card {
    background: #1a1a1a;
    border: 1px solid #333;
}

/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
    .hero.section h2 {
        font-size: 2.1rem;
    }
    .hero.section p {
        font-size: 1rem;
    }
}

@media (max-width: 767px) {
    .hero.section {
        min-height: 80vh;
        padding: 60px 0;
    }
    .hero.section h2 {
        font-size: 1.7rem;
    }
    .btn-get-started {
        padding: 10px 22px;
        font-size: 0.95rem;
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
