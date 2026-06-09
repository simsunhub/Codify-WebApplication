<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'order'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with(['user', 'order.orderItems.course'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }
}
