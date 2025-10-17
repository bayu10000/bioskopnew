@extends('layouts.template')

@section('content')

{{-- 1. HERO SECTION (Dengan Multiple Background Video) --}}
<section id="hero" class="hero section dark-background">
    {{-- Wrapper background video slideshow --}}
    <div id="hero-video-slideshow">
        {{-- Video 1: Tambahkan class 'active' agar video ini diputar pertama --}}
        <video class="hero-video-slide active" autoplay loop muted playsinline>
            <source src="{{ asset('videos/marvel.mp4') }}" type="video/mp4">
            {{-- <source src="{{ asset('video/random_hero_bg_1.webm') }}" type="video/webm"> --}}
        </video>
        <video class="hero-video-slide" loop muted playsinline>
            <source src="{{ asset('videos/wano.mp4') }}" type="video/mp4">
            {{-- <source src="{{ asset('video/random_hero_bg_1.webm') }}" type="video/webm"> --}}
        </video>

        {{-- Video 2 --}}
        <video class="hero-video-slide" loop muted playsinline>
            <source src="{{ asset('videos/demon.mp4') }}" type="video/mp4">
            {{-- <source src="{{ asset('video/random_hero_bg_2.webm') }}" type="video/webm"> --}}
        </video>

        {{-- Video 3, dst. Anda bisa menambahkannya di sini --}}
        <video class="hero-video-slide" loop muted playsinline>
            <source src="{{ asset('videos/tom.mp4') }}" type="video/mp4">
            {{-- <source src="{{ asset('video/random_hero_bg_3.webm') }}" type="video/webm"> --}}
        </video>
        <video class="hero-video-slide" loop muted playsinline>
            <source src="{{ asset('videos/star.mp4') }}" type="video/mp4">
            {{-- <source src="{{ asset('video/random_hero_bg_3.webm') }}" type="video/webm"> --}}
        </video>
    </div>

    {{-- Overlay --}}
    <div class="hero-gradient-overlay"></div>

    {{-- Konten Utama Hero Section --}}
    {{-- <div class="container d-flex flex-column align-items-center justify-content-center">
        <h2 data-aos="fade-up" data-aos-delay="100">SELAMAT DATANG DI CINEPHILE!</h2>
        <p data-aos="fade-up" data-aos-delay="200">Lebih dari sekadar menonton, ini adalah pengalaman sinematik.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('home') }}" class="btn-get-started">LIHAT FILM</a>
        </div>
    </div> --}}
</section>
{{-- END HERO SECTION --}}

<hr>

{{-- 2. KENAPA MEMILIH CINEPHILE (Tidak ada perubahan) --}}
<section class="anime-details spad" style="padding-top: 50px;">
    {{-- ... (Isi section ini tetap sama seperti sebelumnya) ... --}}
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

<hr>

{{-- 3. SCRIPT SLIDESHOW VIDEO --}}
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const videos = document.querySelectorAll(".hero-video-slide");
        let current = 0;
        const intervalTime = 8000; // Ganti video setiap 8 detik

        function nextVideo() {
            // 1. Dapatkan video aktif saat ini
            const currentVideo = videos[current];
            
            // 2. Hapus status 'active' dan PAUSE video saat ini
            currentVideo.classList.remove("active");
            currentVideo.pause(); 
            
            // 3. Pindah ke indeks video berikutnya
            current = (current + 1) % videos.length;
            
            // 4. Dapatkan video berikutnya
            const nextVideo = videos[current];

            // 5. Tambahkan status 'active' dan PLAY video berikutnya
            nextVideo.classList.add("active");
            nextVideo.currentTime = 0; // Pastikan video mulai dari awal
            nextVideo.play().catch(error => {
                 // Menangani error jika play() gagal (misalnya, jika browser memblokir)
                 console.error("Video play failed:", error);
            });
        }

        // Mulai interval slideshow setelah DOM dimuat
        setInterval(nextVideo, intervalTime);

        // Pastikan video pertama diputar saat halaman dimuat (untuk browser yang mendukung)
        videos[0].play().catch(error => {
             console.error("Initial video play failed:", error);
        });
    });
</script>

<hr>

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

/* Background video slideshow wrapper - BARU */
#hero-video-slideshow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1; 
    overflow: hidden;
}

/* Tiap elemen video slide - BARU */
.hero-video-slide {
    position: absolute;
    top: 50%;
    left: 50%;
    /* Triks untuk memastikan video menutupi seluruh background sambil mempertahankan aspek rasio */
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    transform: translate(-50%, -50%);
    object-fit: cover;
    
    /* Efek transisi untuk pergantian video */
    opacity: 0;
    transition: opacity 1.2s ease-in-out; 
}

/* Video aktif (yang sedang ditampilkan) */
.hero-video-slide.active {
    opacity: 1;
}

/* Overlay gradasi (Tidak Berubah) */
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

/* Konten di atas slide (Tidak Berubah) */
.hero.section .container {
    position: relative;
    z-index: 3;
    padding: 0 15px;
}

/* ... (Style H2, P, dan Button lainnya tetap sama) ... */
.hero.section h2 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
/* ... (lanjutan style di bawah) ... */
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

/* ==== CARD SECTION (Tidak Berubah) ==== */
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

/* ==== RESPONSIVE (Tidak Berubah) ==== */
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