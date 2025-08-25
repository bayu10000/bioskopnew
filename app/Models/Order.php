<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'showtime_id',
        'jumlah_tiket',
        'total_harga',
        'tanggal',
        'status',
    ];

    // Relasi ke Showtime
    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    // Relasi ke User (kalau pakai Auth)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke OrderDetail
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
