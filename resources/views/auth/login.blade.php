@extends('layouts.template')

@section('content')
<section class="normal-breadcrumb spad set-bg" data-setbg="{{ asset('img/normal-breadcrumb.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Login</h2>
                    <p>Welcome to the official Anime blog.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="login spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login__form">
                    <h3>Login</h3>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="input__item">
                            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" name="password" placeholder="Password" required>
                            <span class="icon_lock"></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-white"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ingat saya</label>
                        </div>
                        <button type="submit" class="site-btn">Login Now</button>
                    </form>
                   
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3>Belum punya akun?</h3>
                    <a href="{{ route('register.form') }}" class="primary-btn">Register Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection