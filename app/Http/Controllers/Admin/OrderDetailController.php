<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;

class OrderDetailController extends Controller
{
    public function index()
    {
        $details = OrderDetail::with([
            'order.user',
            'seat',
            'showtime.film',
        ])->get();

        return view('admin.order-details.index', compact('details'));
    }
}
