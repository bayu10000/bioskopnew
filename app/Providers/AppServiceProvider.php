<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {

        Carbon::setLocale('id');
        // Gate::before agar super-admin selalu dapat akses
        Gate::before(function ($user, $ability) {
            // Pastikan $user bukan null dan adalah instance model User
            if (! $user) {
                return null;
            }

            // Jika User punya method isSuperAdmin(), gunakan itu
            if (method_exists($user, 'isSuperAdmin')) {
                try {
                    if ($user->isSuperAdmin()) {
                        return true;
                    }
                } catch (\Throwable $e) {
                    // jika ada error, jangan crash aplikasi
                    return null;
                }
            }

            // Kalau ada hasRole (Spatie), cek role 'super-admin' atau 'admin'
            if (method_exists($user, 'hasRole')) {
                try {
                    if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                        return true;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            return null; // biarkan Gate meneruskan pengecekan normal
        });
    }
}
