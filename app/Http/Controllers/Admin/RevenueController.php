<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\WithdrawRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index()
    {
        $totalSales = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'completed');
        })->sum('price');

        $systemEarning = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'completed');
        })->sum('platform_fee');

        $instructorEarning = OrderItem::whereHas('order', function ($q) {
            $q->where('status', 'completed');
        })->sum('instructor_earning');

        $withdrawals = WithdrawRequest::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.revenue.index', compact('totalSales', 'systemEarning', 'instructorEarning', 'withdrawals'));
    }

    public function approveWithdraw($id)
    {
        $w = WithdrawRequest::findOrFail($id);
        $w->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Notify teacher
        Notification::create([
            'user_id' => $w->user_id,
            'type' => 'message',
            'title' => __('messages.revenue.withdrawal_approved_title'),
            'body' => __('messages.revenue.withdrawal_approved', ['amount' => $w->amount, 'method' => strtoupper($w->payment_method)]),
            'url' => route('teacher.revenue.index'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.revenue.index')->with('success', __('Funds transferred and request closed.'));
    }

    public function rejectWithdraw(Request $request, $id)
    {
        $w = WithdrawRequest::findOrFail($id);

        $request->validate(['admin_notes' => 'required|string']);

        $w->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_at' => now(),
        ]);

        // Notify teacher
        Notification::create([
            'user_id' => $w->user_id,
            'type' => 'comment',
            'title' => __('messages.revenue.withdrawal_rejected_title'),
            'body' => __('messages.revenue.withdrawal_rejected', ['amount' => $w->amount, 'notes' => $request->admin_notes]),
            'url' => route('teacher.revenue.index'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.revenue.index')->with('success', __('The request was denied.'));
    }
}