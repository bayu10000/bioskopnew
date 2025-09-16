<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = ['judul', 'sinopsis', 'durasi', 'link_trailer', 'poster', 'tanggal_mulai', 'tanggal_selesai'];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
