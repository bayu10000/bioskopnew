@extends('layouts.template')

@section('content')

<section id="hero" class="hero section dark-background">
    {{-- Gambar latar belakang statis. Ganti 'hero-bg.jpg' dengan nama file gambar Anda --}}
    <img src="{{ asset('img/back.png') }}" alt="Background" data-aos="fade-in">
    
    {{-- Overlay Gradasi Transparan --}}
    <div class="hero-gradient-overlay"></div>

    <div class="container d-flex flex-column align-items-center">
        <h2 data-aos="fade-up" data-aos-delay="100">SELAMAT DATANG DI CHINEPHILE!</h2>
        <p data-aos="fade-up" data-aos-delay="200">Lebih dari sekadar menonton, ini adalah pengalaman sinematik.</p>
        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('home') }}" class="btn-get-started">LIHAT FILM</a>
            {{-- Ganti URL YouTube dengan trailer film yang ingin ditampilkan --}}
            
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
                <div class="showtime-card p-5 text-center">
                    <div class="row mt-5">
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

/* Gaya untuk gradasi transparan */
.hero-gradient-overlay {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 300px; /* Atur ketinggian gradasi sesuai kebutuhan */
    background: linear-gradient(to top, rgba(13, 13, 13, 1), rgba(13, 13, 13, 0));
    z-index: 3; /* Pastikan di atas gambar latar belakang */
}

.hero.section .container {
    position: relative;
    z-index: 4; /* Pastikan konten teks dan tombol di atas overlay */
}

.hero.section h2 {
    font-size: 3rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 20px;
    text-transform: uppercase;
}

.hero.section p {
    font-size: 1.25rem;
    color: #fff;
    max-width: 600px;
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

.btn-watch-video {
    text-decoration: none;
    color: #fff;
    margin-left: 15px;
    font-size: 1.1rem;
    font-weight: 600;
}

.btn-watch-video i {
    font-size: 2rem;
    line-height: 0;
    margin-right: 8px;
    color: #e53637;
    transition: 0.3s;
}

.btn-watch-video:hover i {
    color: #ff5252;
}

/* Styling untuk konten di bawah hero section */
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

</style>
@endsection

{{-- Pustaka eksternal yang dibutuhkan oleh Hero Section --}}
@push('head_scripts')
    {{-- Aos CSS --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    {{-- Glightbox CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">
    {{-- Bootstrap Icons CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush

@push('scripts')
    {{-- Aos JS --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    {{-- Glightbox JS --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <script>
        // Inisialisasi AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        // Inisialisasi Glightbox
        const glightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true,
            videosWidth: '900px',
            videosHeight: '500px'
        });
    </script>
@endpush