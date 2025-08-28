<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    protected $fillable = ['film_id', 'ruangan_id', 'tanggal', 'jam', 'harga'];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
