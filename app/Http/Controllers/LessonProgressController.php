<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonProgressController extends Controller
{
    /**
     * Complete a lesson and generate a certificate if all lessons are completed.
     */
    public function complete(Request $request, Lesson $lesson)
    {
        $user = auth()->user();

        // 1. Mark lesson as completed
        LessonProgress::updateOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ], [
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        // 2. Check course completion
        $course = $lesson->course;
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $certificateEarned = false;
        if ($totalLessons > 0 && $completedLessons >= $totalLessons) {
            // Check if certificate already generated
            $existing = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$existing) {
                Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'code' => 'CERT-' . strtoupper(Str::random(10)),
                    'issued_at' => now(),
                ]);
                $certificateEarned = true;
            }
        }

        $progressPercent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'completed' => true,
                'completedCount' => $completedLessons,
                'totalLessons' => $totalLessons,
                'progress' => $progressPercent,
                'certificateEarned' => $certificateEarned,
                'message' => __('messages.progress.lesson_completed'),
            ]);
        }

        return redirect()->back()->with('success', __('messages.progress.lesson_completed'));
    }

    /**
     * Mark a lesson as incomplete.
     */
    public function uncomplete(Request $request, Lesson $lesson)
    {
        $user = auth()->user();

        LessonProgress::updateOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ], [
            'is_completed' => false,
            'completed_at' => null,
        ]);

        // Recalculate course progress
        $course = $lesson->course;
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $progressPercent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'completed' => false,
                'completedCount' => $completedLessons,
                'totalLessons' => $totalLessons,
                'progress' => $progressPercent,
                'message' => __('messages.progress.lesson_incomplete'),
            ]);
        }

        return redirect()->back()->with('success', __('messages.progress.lesson_incomplete'));
    }
}
