<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'sinopsis', 'durasi', 'link_trailer', 'poster', 'tanggal_mulai', 'tanggal_selesai', 'aktor', 'sutradara'];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    // 💡 RELASI BARU: Menghubungkan Film ke Order melalui Showtime
    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,     // Model tujuan akhir
            Showtime::class,  // Model perantara
            'film_id',        // Foreign key di tabel 'showtimes' (menghubungkan ke film)
            'showtime_id',    // Foreign key di tabel 'orders' (menghubungkan ke showtime)
            'id',             // Local key di tabel 'films'
            'id'              // Local key di tabel 'showtimes'
        );
    }

    // Accessor yang sekarang akan berfungsi karena relasi orders() sudah ada
    public function getTiketTerjualAttribute()
    {
        // Hitung total dari kolom 'jumlah_tiket' untuk pesanan yang sudah lunas ('paid')
        return $this->orders()
            ->where('status', 'paid')
            ->sum('jumlah_tiket') ?? 0;
    }
}
