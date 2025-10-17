@extends('layouts.template')

@section('content')
<section class="normal-breadcrumb spad set-bg" data-setbg="{{ asset('img/regis.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    {{-- Anda bisa menambahkan teks di sini, contoh: <h2>Daftar Akun Baru</h2> --}}
                </div>
            </div>
        </div>
    </div>
</section>

---

<section class="signup spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login__form">
                    <h3>Register</h3>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- 1. FIELD NAMA LENGKAP (NAME) - Tetap menjadi input teks --}}
                        <div class="input__item">
                            <input type="text" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                            <span class="icon_profile"></span>
                        </div>
                        
                        {{-- 2. FIELD USERNAME - Baru ditambahkan --}}
                        <div class="input__item">
                            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
                            <span class="icon_tag"></span> 
                        </div>

                        {{-- 3. FIELD EMAIL (Tidak Berubah) --}}
                        <div class="input__item">
                            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                            <span class="icon_mail"></span>
                        </div>
                        
                        {{-- 4. FIELD PASSWORD (Tidak Berubah) --}}
                        <div class="input__item">
                            <input type="password" name="password" placeholder="Password" required>
                            <span class="icon_lock"></span>
                        </div>
                        
                        {{-- 5. FIELD KONFIRMASI PASSWORD (Tidak Berubah) --}}
                        <div class="input__item">
                            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
                            <span class="icon_lock"></span>
                        </div>
                        
                        <button type="submit" class="site-btn">Register Now</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3>Sudah punya akun?</h3>
                    <a href="{{ route('login.form') }}" class="primary-btn">Login Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

{{-- STYLE TAMBAHAN UNTUK SELECT FIELD --}}
<style>
/* Style tambahan agar select field (untuk level user) terlihat bagus di dalam div.input__item */
.login__form .input__item .input__select {
    width: 100%;
    padding: 10px 0;
    border: none;
    background: transparent;
    color: #fff; /* Sesuaikan dengan warna teks form Anda */
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 15px;
    height: 40px;
    -webkit-appearance: none; /* Hapus panah default browser */
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
}

.login__form .input__item .input__select:focus {
    outline: none;
    border-bottom: 1px solid #e53637; /* Sesuaikan dengan warna fokus Anda */
}

/* Untuk membuat teks placeholder di select terlihat buram sebelum dipilih */
.login__form .input__item .input__select option:first-child {
    color: rgba(255, 255, 255, 0.5); 
}

/* Mengatur warna teks option (pilihan) di dropdown */
.login__form .input__item .input__select option {
    background: #0b0c2a; /* Sesuaikan dengan background form Anda */
    color: #fff;
}
</style>