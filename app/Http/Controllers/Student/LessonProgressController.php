<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonProgressController extends Controller
{
    public function toggleComplete(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $lesson = Lesson::findOrFail($id);

        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($progress) {
            $newCompleted = !$progress->is_completed;
            $progress->update([
                'is_completed' => $newCompleted,
                'completed_at' => $newCompleted ? now() : null,
            ]);
        } else {
            $newCompleted = true;
            $progress = LessonProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        $course = $lesson->course;
        $totalLessons = $course->lessons()->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->where('is_completed', true)
            ->count();

        $certificateEarned = false;
        if ($newCompleted && $totalLessons > 0 && $completedLessons >= $totalLessons) {
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

        return response()->json([
            'success' => true,
            'completed' => $newCompleted,
            'completedCount' => $completedLessons,
            'totalLessons' => $totalLessons,
            'progress' => $progressPercent,
            'certificateEarned' => $certificateEarned,
        ]);
    }
}
