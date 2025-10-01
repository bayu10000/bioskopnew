<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ruangan extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kapasitas'];

    // Relasi: Ruangan punya banyak kursi
    public function seats()
    {
        return $this->hasMany(Seat::class, 'ruangan_id');
    }

    // Relasi: Ruangan punya banyak jadwal tayang
    public function showtimes()
    {
        return $this->hasMany(Showtime::class, 'ruangan_id');
    }
}
