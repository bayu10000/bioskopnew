<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    */
    'broadcasting' => [

        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    */
    'assets_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    */
    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    */
    'livewire_loading_delay' => 'default',

    /*
    |--------------------------------------------------------------------------
    | System Route Prefix
    |--------------------------------------------------------------------------
    */
    'system_route_prefix' => 'filament',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    | Konfigurasi agar hanya user dengan role "admin" yang bisa masuk Filament.
    */
    'auth' => [
        'guard' => 'web',
        'middleware' => [
            'auth',
            \App\Http\Middleware\EnsureUserIsAdmin::class, // pastikan middleware sudah dibuat
        ],
    ],

];
