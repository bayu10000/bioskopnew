<x-layout>
    <h1>Login</h1>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Masuk</button>
    </form>

    <p>
        Belum punya akun? 
        <a href="{{ route('register') }}" 
           style="display:inline-block; padding:8px 12px; background:#3490dc; color:#fff; border-radius:4px; text-decoration:none;">
           Registrasi
        </a>
    </p>
    
</x-layout>
