<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\Notification;

class EnrollmentController extends Controller
{
    /**
     * Handle enrollment request.
     */
    public function enroll(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();

        // 1. Prevent author from enrolling in their own course
        if ($course->user_id === $user->id) {
            return back()->with('error', __('You cannot enroll in your own course.'));
        }

        // 2. Check if already enrolled
        if ($course->isEnrolledBy($user->id)) {
            return redirect()->route('course.learn', $course->slug)
                ->with('info', __('You are already enrolled in this course.'));
        }

        // 3. Paid Course check
        if ($course->price > 0) {
            return redirect()->route('course.checkout', $course->slug);
        }

        // 4. Free Course: instant enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        // Trigger dynamic notification
        try {
            $teacher = $course->user;
            Notification::create([
                'user_id' => $teacher->id,
                'type' => 'enrollment',
                'title' => __('New student enrolled!'),
                'body' => __(':name has enrolled in your course ":title"', [
                    'name' => $user->name,
                    'title' => $course->title
                ]),
                'url' => route('teacher.courses.show', $course->id),
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Silence if migration not yet completed
        }

        return redirect()->route('course.learn', $course->slug)
            ->with('success', __('You have successfully enrolled in course ":title"!', ['title' => $course->title]));
    }

    /**
     * Show mock checkout page for paid courses.
     */
    public function checkout($slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();

        if ($course->isEnrolledBy($user->id)) {
            return redirect()->route('course.learn', $course->slug)
                ->with('info', __('You are already enrolled in this course.'));
        }

        return view('pages.checkout', compact('course'));
    }

    /**
     * Process mock payment and complete enrollment.
     */
    public function processCheckout(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $user = auth()->user();

        if ($course->isEnrolledBy($user->id)) {
            return redirect()->route('course.learn', $course->slug);
        }

        // Validate mock card info
        $request->validate([
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvc' => 'required|string',
        ]);

        // Complete enrollment (simulation successful)
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        // Trigger dynamic notification
        try {
            $teacher = $course->user;
            Notification::create([
                'user_id' => $teacher->id,
                'type' => 'enrollment',
                'title' => __('New course sale!'),
                'body' => __(':name has purchased your course ":title"', [
                    'name' => $user->name,
                    'title' => $course->title
                ]),
                'url' => route('teacher.courses.show', $course->id),
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Silence
        }

        return redirect()->route('course.learn', $course->slug)
            ->with('success', __('Payment successful! Welcome to the course ":title"!', ['title' => $course->title]));
    }

    /**
     * Unenroll student.
     */
    public function unenroll(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $user = auth()->user();

        Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->delete();

        return redirect()->route('course.show', $course->slug)
            ->with('success', __('You have successfully unenrolled from the course ":title".', ['title' => $course->title]));
    }
}
