<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    // Simpan hanya foreign keys; tanggal/jam diambil dari relasi showtime
    protected $fillable = [
        'order_id',
        'user_id',
        'showtime_id',
        'seat_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
    public function film()
{
    return $this->belongsTo(Film::class);
}

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
