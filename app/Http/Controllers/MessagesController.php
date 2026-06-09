<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get all unique users the current user has exchanged messages with
        $userIds = Message::where('sender_id', $user->id)
            ->pluck('receiver_id')
            ->merge(
                Message::where('receiver_id', $user->id)->pluck('sender_id')
            )
            ->unique();

        $conversations = User::whereIn('id', $userIds)->get()->map(function ($contact) use ($user) {
            $lastMessage = Message::where(function ($q) use ($user, $contact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
            })->orWhere(function ($q) use ($user, $contact) {
                $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
            })->latest()->first();

            $unreadCount = Message::where('sender_id', $contact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();

            $contact->last_message = $lastMessage;
            $contact->unread_count = $unreadCount;
            return $contact;
        })->sortByDesc(function ($contact) {
            return $contact->last_message ? $contact->last_message->created_at : now();
        })->values();

        // Get eligible recipients for starting a new chat
        $recipients = collect();
        if ($user->isAdmin()) {
            $recipients = User::where('id', '!=', $user->id)->orderBy('name')->get();
        } elseif ($user->role === 'teacher') {
            // Get all students enrolled in courses taught by this teacher
            $studentIds = \App\Models\Enrollment::whereIn('course_id', function ($q) use ($user) {
                $q->select('id')->from('courses')->where('instructor_id', $user->id);
            })->pluck('user_id')->unique();
            $recipients = User::whereIn('id', $studentIds)->orderBy('name')->get();
        } else {
            // Get all teachers of courses this student is enrolled in
            $teacherIds = \App\Models\Course::whereIn('id', function ($q) use ($user) {
                $q->select('course_id')->from('enrollments')->where('user_id', $user->id);
            })->pluck('instructor_id')->unique();
            $recipients = User::whereIn('id', $teacherIds)->orderBy('name')->get();
        }

        return view('student.messages.index', compact('conversations', 'recipients'));
    }

    /**
     * Display the specified conversation's messages.
     */
    public function show($userId)
    {
        $user = auth()->user();
        $contact = User::findOrFail($userId);

        $messages = Message::where(function ($q) use ($user, $contact) {
            $q->where('sender_id', $user->id)->where('receiver_id', $contact->id);
        })->orWhere(function ($q) use ($user, $contact) {
            $q->where('sender_id', $contact->id)->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        // Mark as read
        Message::where('sender_id', $contact->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'contact' => $contact,
            'messages' => $messages
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $sender = auth()->user();

        if ($sender->id == $request->receiver_id) {
            return response()->json(['success' => false, 'error' => __('You cannot send a message to yourself.')], 400);
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $request->receiver_id,
            'course_id' => null,
            'subject' => 'Chat Message',
            'body' => $request->body,
            'is_read' => false,
        ]);

        // Create notification
        try {
            Notification::create([
                'user_id' => $request->receiver_id,
                'type' => 'message',
                'title' => __('New message from :name', ['name' => $sender->name]),
                'body' => \Illuminate\Support\Str::limit($request->body, 50),
                'url' => route('messages.index') . '?user=' . $sender->id,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', __('Message sent!'));
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message)
    {
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            abort(403);
        }
        $message->delete();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', __('The message has been deleted.'));
    }
}