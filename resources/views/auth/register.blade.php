<x-layout>
    <h1>Register</h1>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nama" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
        <button type="submit">Daftar</button>
    </form>

    <p>
        Sudah Punya Akun? 
        <a href="{{ route('login') }}" 
           style="display:inline-block; padding:8px 12px; background:#3490dc; color:#fff; border-radius:4px; text-decoration:none;">
           Login
        </a>
    </p>
</x-layout>
