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
    
    {{-- Placeholder untuk CSS tambahan dari halaman lain --}}
    @stack('head_scripts')

    {{-- CSS tambahan untuk garis bawah merah --}}
    <style>
        .header__menu ul li.active a {
            background-color: transparent !important;
            color: #ffffff !important;
            border-bottom: 2px solid #e53637; /* Add red underline */
            padding-bottom: 5px; /* Adjust spacing as needed */
            padding-top: 0;
            padding-left: 0;
            padding-right: 0;
        }
    </style>
</head>

<body>
    {{-- Header Section Begin --}}
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="{{ route('home') }}" class="logo-text">
                            <span class="chine">CINE</span><span class="phile">PHILE</span>
                        </a>
                        
                        <style>
                        .logo-text {
                            font-size: 1.8rem;      /* ukuran font */
                            font-weight: 800;        /* ketebalan */
                            text-decoration: none;   /* hilangkan underline */
                            font-family: 'Poppins', sans-serif; /* bisa ganti sesuai selera */
                            margin: 0%;
                        }
                        
                        .logo-text .chine {
                            color: #e53637; /* merah */
                        }
                        
                        .logo-text .phile {
                            color: #ffffff; /* putih */
                        }
                        </style>
                        
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="header__nav">
                        <nav class="header__menu mobile-menu">
                            <ul>
                                <li class="{{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                                    <a href="{{ route('profile') }}">Homepage</a>
                                </li>
                                <li class="{{ in_array(Route::currentRouteName(), ['home', 'film.show']) ? 'active' : '' }}">
                                    <a href="{{ route('home') }}">Film</a>
                                </li>
                                
                                {{-- <li><a href="#">Genre <span class="arrow_carrot-down"></span></a>
                                    <ul class="dropdown">
                                        @php
                                            $genres = \App\Models\Genre::all();
                                        @endphp
                                        @foreach($genres as $genre)
                                            <li><a href="{{ route('home', ['genre' => $genre->id]) }}">{{ $genre->nama_genre }}</a></li>
                                        @endforeach
                                    </ul> --}}
                                </li>
                                @auth
                                    <li class="{{ Route::currentRouteName() == 'my-orders' ? 'active' : '' }}"><a href="{{ route('my-orders') }}">Pesanan Saya</a></li>
                                @endauth
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="header__right">
                        @guest
                            
                            <a href="{{ route('login.form') }}">Login</a>
                        @endguest

                        @auth
                            
                            {{-- <a href="#">{{ Auth::user()->name }}</a> --}}
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    {{-- Header Section End --}}

    {{-- Content Section --}}
    @yield('content')

    {{-- Footer Section Begin --}}
    <footer class="footer">
        <div class="page-up">
            <a href="#" id="scrollToTopButton"><span class="arrow_carrot-up"></span></a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    {{-- <div class="footer__logo">
                        <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt=""></a>
                    </div> --}}
                </div>
                <div class="col-lg-6">
                    <div class="footer__nav">
                        <ul>
                            <li class="{{ Route::currentRouteName() == 'home' ? 'active' : '' }}"><a href="{{ route('home') }}">Homepage</a></li>
                            <li><a href="#">Categories</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">
                    <p>
                        Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    {{-- Footer Section End --}}

    {{-- Script Bawaan Template --}}
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/player.js') }}"></script>
    <script src="{{ asset('js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/mixitup.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    
    {{-- Placeholder untuk JS tambahan dari halaman lain --}}
    @stack('scripts')
</body>

</html>