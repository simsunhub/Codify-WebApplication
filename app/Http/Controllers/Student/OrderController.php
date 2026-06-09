<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $orders = Order::where('user_id', $userId)
            ->with('items.course')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $userId = auth()->id();
        $order = Order::where('user_id', $userId)
            ->with(['items.course', 'coupon', 'payment'])
            ->findOrFail($id);

        return view('student.orders.show', compact('order'));
    }
}
