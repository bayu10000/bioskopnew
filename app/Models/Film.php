<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = ['judul', 'sinopsis', 'genre', 'durasi', 'link_trailer', 'poster'];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
