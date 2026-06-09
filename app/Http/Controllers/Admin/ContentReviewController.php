<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class ContentReviewController extends Controller
{
    public function index()
    {
        $courses = Course::with(['instructor', 'category'])
            ->where('status', 'draft')
            ->latest()
            ->paginate(10);

        return view('admin.content-review.index', compact('courses'));
    }

    public function approve($id)
    {
        $course = Course::findOrFail($id);
        $course->status = 'published';
        $course->save();

        return redirect()->route('admin.content-review.index')
            ->with('success', 'Course approved and published successfully!');
    }

    public function reject($id)
    {
        $course = Course::findOrFail($id);
        // Delete the course if rejected, or we can just delete it
        $course->delete();

        return redirect()->route('admin.content-review.index')
            ->with('success', 'Course has been rejected and removed.');
    }
}
