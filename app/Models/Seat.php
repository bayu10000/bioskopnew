<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['showtime_id', 'nomor_kursi', 'status'];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
