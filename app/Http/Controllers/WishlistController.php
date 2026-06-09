<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display student's wishlist courses.
     */
    public function index()
    {
        $user = auth()->user();

        // Get wishlisted courses with categories and reviews
        $wishlists = Wishlist::with(['course.category', 'course.user', 'course.reviews'])
            ->where('user_id', $user->id)
            ->whereHas('course', function($q) {
                $q->where('is_active', true);
            })
            ->latest()
            ->get();

        return view('student.wishlist', compact('wishlists'));
    }

    /**
     * Toggle wishlist item via AJAX or normal request.
     */
    public function toggle(Course $course)
    {
        $user = auth()->user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $added = false;
            $message = __('messages.wishlist.removed');
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $added = true;
            $message = __('messages.wishlist.added');
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
