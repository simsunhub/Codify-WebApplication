<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['user', 'order'])->latest()->paginate(15);
        $totalSales = Payment::where('status', 'completed')->sum('amount');

        return view('admin.sales.index', compact('payments', 'totalSales'));
    }
}
