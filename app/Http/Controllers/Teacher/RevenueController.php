<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Course;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        $courseIds = Course::where('instructor_id', $teacherId)->orWhere('user_id', $teacherId)->pluck('id');

        $earnings = OrderItem::whereIn('course_id', $courseIds)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('instructor_earning');

        $withdrawals = WithdrawRequest::where('user_id', $teacherId)->orderBy('created_at', 'desc')->get();
        
        $pendingWithdrawal = WithdrawRequest::where('user_id', $teacherId)->where('status', 'pending')->sum('amount');
        $withdrawnAmount = WithdrawRequest::where('user_id', $teacherId)->where('status', 'approved')->sum('amount');
        
        $balance = $earnings - $withdrawnAmount - $pendingWithdrawal;

        return view('teacher.revenue.index', compact('earnings', 'withdrawals', 'balance', 'pendingWithdrawal'));
    }

    public function requestWithdraw(Request $request)
    {
        $teacherId = auth()->id();
        $courseIds = Course::where('instructor_id', $teacherId)->orWhere('user_id', $teacherId)->pluck('id');

        $earnings = OrderItem::whereIn('course_id', $courseIds)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('instructor_earning');

        $pendingWithdrawal = WithdrawRequest::where('user_id', $teacherId)->where('status', 'pending')->sum('amount');
        $withdrawnAmount = WithdrawRequest::where('user_id', $teacherId)->where('status', 'approved')->sum('amount');
        $balance = $earnings - $withdrawnAmount - $pendingWithdrawal;

        $request->validate([
            'amount' => 'required|numeric|min:10|max:' . $balance,
            'payment_method' => 'required|string',
            'payment_details' => 'required|string',
        ]);

        WithdrawRequest::create([
            'user_id' => $teacherId,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_details' => ['details' => $request->payment_details],
            'status' => 'pending',
        ]);

        return redirect()->route('teacher.revenue.index')->with('success', __('Purchase offer sent!'));
    }
}