@extends('layouts.template')

@section('content')
<section class="normal-breadcrumb spad set-bg" data-setbg="{{ asset('img/regis.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    
                </div>
            </div>
        </div>
    </div>
</section>
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
                        <div class="input__item">
                            <input type="text" name="name" placeholder="Nama" value="{{ old('name') }}" required>
                            <span class="icon_profile"></span>
                        </div>
                        <div class="input__item">
                            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" name="password" placeholder="Password" required>
                            <span class="icon_lock"></span>
                        </div>
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