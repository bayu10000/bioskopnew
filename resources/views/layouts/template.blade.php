<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bioskop App</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/plyr.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css">
    
    @stack('head_scripts')

    <style>
        /* Active menu dengan garis bawah merah */
        .header__menu ul li.active a {
            background-color: transparent !important;
            color: #ffffff !important;
            border-bottom: 2px solid #e53637;
            padding-bottom: 5px;
        }

        /* Logo */
        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }
        .logo-text .chine { color: #e53637; }
        .logo-text .phile { color: #ffffff; }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .logo-text {
                font-size: 1.3rem;
            }
            /* Hapus style login/logout desktop di mobile karena akan pindah ke menu */
            .header__right {
                 display: none !important;
            }

            .footer__nav ul {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
        /* Custom slicknav burger menu */
        .slicknav_menu {
            background: none !important;
            padding: 0 !important;
        }

        .slicknav_btn {
            background: none !important;
            margin-top: 5px;
        }

        .slicknav_icon-bar {
            background-color: #fff !important; /* jadi putih */
        }

        .slicknav_nav {
            background: #0d1b2a !important; /* biru navy sesuai tema */
            border: none;
        }
        /* Burger Menu Links jadi putih */
        #mobile-menu-wrap ul li a {
            color: #fff !important;  /* teks putih */
            font-weight: 500;
            padding: 10px 15px;
            display: block;
        }

        /* Saat hover lebih jelas */
        #mobile-menu-wrap ul li a:hover {
            color: #e53637 !important; /* merah sesuai tema */
        }

        /* Styling khusus untuk Garis Pembatas Logout di Mobile */
        #mobile-menu-wrap .logout-separator {
            border-top: 1px solid rgba(255, 255, 255, 0.2); /* Garis abu-abu transparan */
            margin-top: 5px;
            padding-top: 5px;
        }
        
        /* Styling teks Logout menjadi merah di Mobile */
        #mobile-menu-wrap .logout-link a {
            color: #e53637 !important; /* Teks merah */
            font-weight: 700 !important;
        }
        
        /* Pastikan hover juga tetap merah/jelas */
        #mobile-menu-wrap .logout-link a:hover {
            background-color: rgba(229, 54, 55, 0.1); /* Sedikit latar belakang merah saat hover */
        }

        /* --- Footer Styles --- */
        .footer {
            color: #fff;
        }
        
        .footer-link {
            color: #ffffff;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            padding-bottom: 2px;
            position: relative;
        }
        .footer-link:hover {
            color: #e53637;
        }
        .footer .active a,
        .footer .active .footer-link {
            color: #e53637 !important;
        }
        .footer .active .footer-link::after {
            content: "";
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e53637;
        }

        /* 💡 BARU: CSS untuk Titik Pemisah di Footer */
        .footer ul li {
            position: relative; /* Penting untuk positioning ::after */
        }
        
        .footer ul li:not(:last-child)::after {
            content: "•"; /* Karakter titik (bullet point) */
            color: #ccc; /* Warna pemisah */
            position: absolute;
            right: -14px; /* Sesuaikan posisi agar titik ada di tengah jarak antar link */
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Atur ulang jarak antar li agar tidak tumpang tindih dengan titik */
        .footer ul {
            gap: 20px !important; /* Kurangi gap jika terlalu lebar, atau sesuaikan right:-14px di atas */
        }

        @media (max-width: 767px) {
             /* Hapus titik pemisah di mobile (karena link akan menumpuk vertikal) */
            .footer ul li:not(:last-child)::after {
                content: none;
            }
             /* Kembalikan gap di mobile untuk tumpukan vertikal */
            .footer ul {
                gap: 8px !important; /* Gap default untuk flex-direction: column */
            }
            .footer .logo-text {
                font-size: 1.3rem;
            }
            .footer-link {
                font-size: 0.9rem;
            }
            .footer p {
                font-size: 0.8rem;
            }
        }

        /* Perbaikan styling dasar (jika hilang di custom CSS) */
        .header__menu ul {
        margin: 0;
        padding: 0;
        }

        .header__menu li {
            list-style: none;
        }

        .header__menu a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 5px 0;
            position: relative;
        }

        /* Hover */
        .header__menu a:hover {
            color: #e53637;
        }

        /* Active menu item */
        .header__menu li.active a {
            color: #e53637;
        }

        /* Active indicator (garis bawah) */
        .header__menu li.active a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 100%;
            height: 2px;
            background: #e53637;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="header__logo">
                        {{-- Link ke profil/homepage --}}
                        <a href="{{ route('profile') }}" class="logo-text">
                            <span class="chine">CINE</span><span class="phile">PHILE</span>
                        </a>
                    </div>
                </div>
    
                {{-- Menu Desktop (tampil di layar besar) --}}
                <div class="col-lg-8 d-none d-lg-flex justify-content-center">
                    <nav class="header__menu">
                        <ul>
                            {{-- Homepage --}}
                            <li class="{{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                                <a href="{{ route('profile') }}">Homepage</a>
                            </li>
                            {{-- Film (aktif untuk index, show, order form, dan order store) --}}
                            <li class="{{ in_array(Route::currentRouteName(), ['home', 'film.show', 'order.show', 'order.store']) ? 'active' : '' }}">
                                <a href="{{ route('home') }}">Film</a>
                            </li>
                            @auth
                                {{-- Pesanan Saya (hanya untuk user yang login) --}}
                                <li class="{{ Route::currentRouteName() == 'my-orders' ? 'active' : '' }}">
                                    <a href="{{ route('my-orders') }}">Pesanan Saya</a>
                                </li>
                                {{-- Tautan Logout untuk menu mobile (disembunyikan di desktop) --}}
                                <li class="d-lg-none logout-link logout-separator"> 
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                        <i class="fa fa-sign-out me-1"></i> Logout ({{ Auth::user()->name ?? 'User' }})
                                    </a>
                                </li>
                                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @else
                                {{-- Tautan Login untuk menu mobile (disembunyikan di desktop) --}}
                                <li class="d-lg-none {{ Route::currentRouteName() == 'login.form' ? 'active' : '' }}">
                                    <a href="{{ route('login.form') }}">Login</a>
                                </li>
                            @endauth
                        </ul>
                    </nav>
                </div>
    
                {{-- Tombol Login/Logout Desktop & Trigger Mobile Menu --}}
                <div class="col-6 col-md-3 col-lg-2 text-end">
                    <div class="header__right d-none d-md-block">
                        @guest
                            <a href="{{ route('login.form') }}">Login</a>
                        @endguest
                        @auth
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();">
                                <i class="fa fa-user"></i> Logout
                            </a>
                            <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </div>
                    {{-- Tombol Burger Menu (hanya tampil di mobile/tablet) --}}
                    <div class="d-lg-none d-block text-end">
                        <div id="mobile-menu-trigger" class="slicknav_btn">
                            <span class="slicknav_icon">
                                <span class="slicknav_icon-bar"></span>
                                <span class="slicknav_icon-bar"></span>
                                <span class="slicknav_icon-bar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    
            {{-- Wrapper untuk Mobile Menu (diisi oleh SlickNav JS) --}}
            <div id="mobile-menu-wrap" class="d-lg-none"></div>
        </div>
    </header>
    
    {{-- Tempat konten halaman spesifik akan di-inject --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="footer py-4 bg-black text-white">
        <div class="container">
            <div class="row align-items-center text-center text-md-start gy-3">
                
                {{-- Logo Footer --}}
                <div class="col-12 col-md-3">
                    <a href="{{ route('profile') }}" class="logo-text d-inline-block">
                        <span class="chine">CINE</span><span class="phile">PHILE</span>
                    </a>
                </div>
    
                {{-- Navigasi Footer --}}
                <div class="col-12 col-md-6">
                    <ul class="list-unstyled d-flex justify-content-center justify-content-md-center gap-4 mb-0 flex-wrap">
                        <li class="{{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                            <a href="{{ route('profile') }}" class="footer-link">Homepage</a>
                        </li>
                    
                        {{-- Tambahkan semua route yang masih dalam kategori "Film" --}}
                        <li class="{{ in_array(Route::currentRouteName(), ['home', 'film.show', 'order.show', 'order.store']) ? 'active' : '' }}">
                            <a href="{{ route('home') }}" class="footer-link">Film</a>
                        </li>
                    
                        <li class="{{ Route::currentRouteName() == 'my-orders' ? 'active' : '' }}">
                            <a href="{{ route('my-orders') }}" class="footer-link">Pesanan Saya</a>
                        </li>
                    </ul>
                    
                </div>
    
                {{-- Copyright --}}
                <div class="col-12 col-md-3">
                    <p class="mb-0 small text-center text-md-end">
                        &copy;<script>document.write(new Date().getFullYear());</script> 
                        <span class="chine">CINE</span><span class="phile">PHILE</span>.  
                        Dibuat dengan <i class="fa fa-heart text-danger"></i>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    {{-- Script JS --}}
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/player.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/mixitup.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        // Aktifkan menu mobile menggunakan SlickNav
        $(document).ready(function () {
            $('.header__menu').slicknav({
                prependTo: '#mobile-menu-wrap',
                allowParentLinks: true
            });
             // Perbaiki pemicu menu burger
            $('#mobile-menu-trigger').on('click', function() {
                // Toggle slicknav_open/close pada #mobile-menu-wrap (biasanya Slicknav akan otomatis melakukannya)
                // Di sini Anda mungkin ingin memicu event klik pada tombol default SlickNav jika ada isu.
                // Namun, karena Anda sudah menggunakan class 'slicknav_btn', asumsikan SlickNav JS menangani ini.
                // Jika ingin manual:
                // $('#mobile-menu-wrap').toggleClass('slicknav_open'); 
            });
        });
    </script>

    @stack('scripts')
</body>
</html>