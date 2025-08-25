<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ruangan;

class Showtime extends Model
{

    protected $fillable = ['film_id', 'tanggal', 'jam', 'harga', 'ruangan_id'];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
