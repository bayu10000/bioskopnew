<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Gaya tambahan untuk font, opsional */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>

<div class="bg-gray-50">
  <div class="min-h-screen flex flex-col items-center justify-center py-6 px-4">
    <div class="max-w-[480px] w-full">
      {{-- <a href="{{ url('/') }}">
        <img src="https://readymadeui.com/readymadeui.svg" alt="logo" class="w-40 mb-8 mx-auto block" />
      </a> --}}
      <div class="p-6 sm:p-8 rounded-2xl bg-white border border-gray-200 shadow-sm">
        <h1 class="text-slate-900 text-center text-3xl font-semibold">Sign in</h1>

        @if ($errors->any())
            <div class="mt-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form class="mt-12 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
          <div>
            <label class="text-slate-900 text-sm font-medium mb-2 block">Email</label>
            <div class="relative flex items-center">
              <input name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full text-slate-900 text-sm border border-slate-300 px-4 py-3 pr-8 rounded-md outline-blue-600" placeholder="Enter your email" />
              <svg xmlns="http://www.w3.org/2000/svg" fill="#bbb" stroke="#bbb" class="w-4 h-4 absolute right-4" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
            </div>
          </div>
          <div>
            <label class="text-slate-900 text-sm font-medium mb-2 block">Password</label>
            <div class="relative flex items-center">
              <input name="password" type="password" required class="w-full text-slate-900 text-sm border border-slate-300 px-4 py-3 pr-8 rounded-md outline-blue-600" placeholder="Enter password" />
              <svg xmlns="http://www.w3.org/2000/svg" fill="#bbb" stroke="#bbb" class="w-4 h-4 absolute right-4 cursor-pointer" viewBox="0 0 128 128">
                <path d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" data-original="#000000"></path>
              </svg>
            </div>
          </div>
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center">
              <input id="remember-me" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 shrink-0 text-blue-600 focus:ring-blue-500 border-slate-300 rounded" />
              <label for="remember-me" class="ml-3 block text-sm text-slate-900">
                Remember me
              </label>
            </div>
         
            </div>
          </div>
          <div class="!mt-12">
            <button type="submit" class="w-full py-2 px-4 text-[15px] font-medium tracking-wide rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none cursor-pointer">
              Sign in
            </button>
          </div>
          <p class="text-slate-900 text-sm !mt-6 text-center">Don't have an account? 
            <a href="{{ route('register.form') }}" class="text-blue-600 hover:underline ml-1 whitespace-nowrap font-semibold">Register here</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>