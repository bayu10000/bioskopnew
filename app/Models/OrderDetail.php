<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = ['order_id', 'seat_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
