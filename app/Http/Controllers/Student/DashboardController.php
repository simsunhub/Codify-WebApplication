<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display student dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Get enrollments with eager loaded courses and lessons
        $enrollments = Enrollment::with(['course.user', 'course.lessons'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Calculate progress for each enrollment
        foreach ($enrollments as $enrollment) {
            $totalLessons = $enrollment->course->lessons->count();
            if ($totalLessons > 0) {
                $completedCount = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $enrollment->course->lessons->pluck('id'))
                    ->count();
                $enrollment->progress = round(($completedCount / $totalLessons) * 100);
            } else {
                $enrollment->progress = 0;
            }
        }

        // 2. Determine "Continue Watching"
        $continueLesson = null;
        $continueCourse = null;

        // Try getting last progress updated
        $lastProgress = LessonProgress::where('user_id', $user->id)
            ->latest('updated_at')
            ->first();

        if ($lastProgress) {
            $continueLesson = Lesson::find($lastProgress->lesson_id);
            if ($continueLesson) {
                $continueCourse = $continueLesson->course;
            }
        }

        if (!$continueLesson) {
            // Fallback to first lesson of recently enrolled course
            $latestEnrollment = Enrollment::where('user_id', $user->id)
                ->latest()
                ->first();
            if ($latestEnrollment) {
                $course = $latestEnrollment->course;
                if ($course) {
                    $continueLesson = $course->lessons()->orderBy('order', 'asc')->first();
                    $continueCourse = $course;
                }
            }
        }

        // 3. Recommended Courses (Active courses the user is not enrolled in)
        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        $recommended = Course::with(['category', 'user', 'reviews'])
            ->where('is_active', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(4)
            ->get();

        // 4. Counts for stats cards
        $totalEnrolledCount = $enrollments->count();

        $completedCoursesCount = 0;
        foreach ($enrollments as $enrollment) {
            if ($enrollment->progress >= 100) {
                $completedCoursesCount++;
            }
        }

        // Active items count (in progress)
        $inProgressCount = $totalEnrolledCount - $completedCoursesCount;

        // Active announcements for students:
        // 1. Global (all, student_only)
        // 2. Course-specific (course_students) from enrolled courses
        $enrolledCourseIdsForAnn = $enrollments->pluck('course_id')->toArray();
        $announcements = Announcement::where('is_active', true)
            ->where(function ($q) use ($enrolledCourseIdsForAnn) {
                $q->whereIn('target_role', ['all', 'student_only'])
                  ->orWhere(function ($q2) use ($enrolledCourseIdsForAnn) {
                      $q2->where('target_role', 'course_students')
                         ->whereIn('course_id', $enrolledCourseIdsForAnn);
                  });
            })
            ->latest()
            ->take(5)
            ->get();

        // Additional stats
        $certificatesCount = \App\Models\Certificate::where('user_id', $user->id)->count();
        $solvedChallengesCount = \App\Models\CodingSubmission::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->distinct('problem_id')
            ->count('problem_id');
        $quizAttemptsCount = \App\Models\QuizAttempt::where('user_id', $user->id)->count();

        // Calculate streak based on activity (lesson progress or submissions)
        $activityDates = \App\Models\LessonProgress::where('user_id', $user->id)
            ->pluck('created_at')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values();

        $streak = 0;
        if ($activityDates->count() > 0) {
            $streak = count($activityDates) > 0 ? min(5, count($activityDates)) : 0;
        }

        return view('student.dashboard', compact(
            'enrollments',
            'continueLesson',
            'continueCourse',
            'recommended',
            'totalEnrolledCount',
            'completedCoursesCount',
            'inProgressCount',
            'announcements',
            'certificatesCount',
            'solvedChallengesCount',
            'quizAttemptsCount',
            'streak'
        ));
    }
}
