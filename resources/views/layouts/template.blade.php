<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bioskop App</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            .header__right a {
                font-size: 0.9rem;
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
    color: #fff !important;   /* teks putih */
    font-weight: 500;
    padding: 10px 15px;
    display: block;
}

/* Saat hover lebih jelas */
#mobile-menu-wrap ul li a:hover {
    color: #e53637 !important; /* merah sesuai tema */
}



    </style>
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="header__logo">
                        <a href="{{ route('profile') }}" class="logo-text">
                            <span class="chine">CINE</span><span class="phile">PHILE</span>
                        </a>
                    </div>
                </div>
    
                <!-- Navigation (desktop) -->
                <div class="col-lg-8 d-none d-lg-flex justify-content-center">
                    <nav class="header__menu">
                        <ul>
                            <li class="{{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                                <a href="{{ route('profile') }}">Homepage</a>
                            </li>
                            <li class="{{ in_array(Route::currentRouteName(), ['home', 'film.show', 'order.show', 'order.store']) ? 'active' : '' }}">
                                <a href="{{ route('home') }}">Film</a>
                            </li>
                            @auth
                                <li class="{{ Route::currentRouteName() == 'my-orders' ? 'active' : '' }}">
                                    <a href="{{ route('my-orders') }}">Pesanan Saya</a>
                                </li>
                            @endauth
                        </ul>
                    </nav>
                </div>
    
                <!-- Login / Logout -->
                <div class="col-6 col-md-3 col-lg-2 text-end">
                    <div class="header__right">
                        @guest
                            <a href="{{ route('login.form') }}">Login</a>
                        @endguest
                        @auth
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
    
            <!-- Burger Menu Wrapper (Mobile) -->
            <div id="mobile-menu-wrap" class="d-lg-none"></div>
        </div>
    </header>
    
    
    
    <!-- Header Section End -->

    <!-- Content Section -->
    @yield('content')

    <!-- Footer Section Begin -->
    <footer class="footer py-4 bg-black text-white">
        <div class="container">
            <div class="row align-items-center text-center text-md-start gy-3">
                
                <!-- Logo -->
                <div class="col-12 col-md-3">
                    <a href="{{ route('profile') }}" class="logo-text d-inline-block">
                        <span class="chine">CINE</span><span class="phile">PHILE</span>
                    </a>
                </div>
    
                <!-- Navigasi Footer -->
                <div class="col-12 col-md-6">
                    <ul class="list-unstyled d-flex justify-content-center justify-content-md-center gap-4 mb-0 flex-wrap">
                        <li class="{{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                            <a href="{{ route('profile') }}" class="footer-link">Homepage</a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'home' ? 'active' : '' }}">
                            <a href="{{ route('home') }}" class="footer-link">Film</a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'my-orders' ? 'active' : '' }}">
                            <a href="{{ route('my-orders') }}" class="footer-link">Pesanan Saya</a>
                        </li>
                    </ul>
                </div>
    
                <!-- Copyright -->
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
    
    {{-- CSS Tambahan --}}
    <style>
    .footer {
         /* jaga full black */
        color: #fff;
    }
    
    .logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
    }
    .logo-text .chine { color: #e53637; } /* merah */
    .logo-text .phile { color: #ffffff; } /* putih */
    
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
    
    @media (max-width: 767px) {
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
    
    
    <!-- Footer Section End -->

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/player.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/mixitup.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        // Aktifkan menu mobile
        $(document).ready(function () {
            $('.header__menu').slicknav({
                prependTo: '#mobile-menu-wrap',
                allowParentLinks: true
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
