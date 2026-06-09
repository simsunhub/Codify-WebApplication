<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\Notification;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'replies.user'])->findOrFail($id);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate(['body' => 'required|string']);

        TicketReply::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $ticket->update([
            'status' => 'replied',
            'updated_at' => now(),
        ]);

        // Notify user
        Notification::create([
            'user_id' => $ticket->user_id,
            'type' => 'message',
            'title' => __('A reply came from technical support'),
            'body' => __("Your query \"{$ticket->subject}\" has been answered."),
            'url' => route('dashboard'), // student dashboard
            'is_read' => false,
        ]);

        return redirect()->route('admin.support.show', $ticket->id)->with('success', __('A reply has been sent.'));
    }

    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'closed']);

        return redirect()->route('admin.support.show', $ticket->id)->with('success', __('The ticket is closed.'));
    }
}