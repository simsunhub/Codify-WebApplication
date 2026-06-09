<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course;

class ReviewController extends Controller
{
    public function index()
    {
        $courseIds = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->pluck('id');
        $reviews = Review::whereIn('course_id', $courseIds)->with(['course', 'user'])->orderBy('created_at', 'desc')->get();
        return view('teacher.reviews.index', compact('reviews'));
    }
}
