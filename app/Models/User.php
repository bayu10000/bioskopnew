<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasSuperAdmin;

    /**
     * Guard default untuk spatie/permission.
     *
     * Kalau kamu pakai multi-guard (misal 'web' untuk user bioskop, 'admin' untuk panel admin),
     * maka sebaiknya definisikan di sini agar tidak bingung.
     */
    protected string $guard_name = 'web'; // ganti jadi 'admin' kalau untuk Filament Admin

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
