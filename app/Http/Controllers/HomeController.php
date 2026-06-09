<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Home page — show featured courses and categories.
     */
    public function index()
    {
        $popularCourses = Course::with(['category', 'user'])
            ->where('status', 'published')
            ->withCount(['reviews', 'enrollments'])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('enrollments_count')
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['courses' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        $instructors = \App\Models\User::whereIn('role', ['teacher', 'instructor'])
            ->withCount(['courses' => function($q) {
                $q->where('is_active', true);
            }])
            ->take(4)
            ->get();

        $testimonials = \App\Models\Review::with(['user', 'course'])
            ->where('rating', 5)
            ->latest()
            ->take(4)
            ->get();

        $heroVideoUrl = \App\Models\SiteSetting::get('hero_video_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&mute=1&loop=1&controls=0');

        $data = compact('popularCourses', 'categories', 'instructors', 'testimonials', 'heroVideoUrl');

        return view('welcome', $data);
    }

    /**
     * Search results page.
     */
    public function search(Request $request)
    {
        $query    = $request->get('q', '');
        $category = $request->get('category', '');
        $sort     = $request->get('sort', 'popular');

        $courses = Course::with(['category', 'user'])
            ->where('status', 'published')
            ->withCount(['reviews', 'enrollments'])
            ->withAvg('reviews', 'rating');

        if ($query) {
            $courses->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if ($category) {
            $courses->where('category_id', $category);
        }

        switch ($sort) {
            case 'newest':
                $courses->orderByDesc('created_at');
                break;
            case 'rating':
                $courses->orderByDesc('reviews_avg_rating');
                break;
            default:
                $courses->orderByDesc('enrollments_count');
        }

        $courses    = $courses->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('search', compact('courses', 'query', 'categories', 'category', 'sort'));
    }

    /**
     * Course detail page.
     */
    public function courseShow($slug)
    {
        $course = Course::with([
                'category', 'user',
                'lessons' => fn($q) => $q->orderBy('order'),
                'modules' => fn($q) => $q->orderBy('sort_order')->with(['lessons' => fn($l) => $l->orderBy('order')]),
                'reviews.user',
            ])
            ->withCount(['reviews', 'enrollments'])
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $isEnrolled = false;
        $userReview = null;
        if (auth()->check()) {
            $isEnrolled = $course->isEnrolledBy(auth()->id());
            $userReview = $course->reviews()->where('user_id', auth()->id())->first();
        }

        return view('courses.show', compact('course', 'isEnrolled', 'userReview'));
    }

    /**
     * Lesson viewer / course player for enrolled students.
     */
    public function learn($slug, ?Lesson $lesson = null)
    {
        $course = Course::with([
                'modules'  => fn($q) => $q->orderBy('sort_order')->with(['lessons' => fn($l) => $l->orderBy('order')]),
                'lessons'  => fn($q) => $q->orderBy('order'),
                'user',
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if (!$lesson) {
            $lesson = $course->lessons->first();
            if (!$lesson) {
                return redirect()->route('course.show', $slug)->with('error', 'This course has no lessons yet.');
            }
        }

        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $user          = auth()->user();
        $isUserAllowed = false;
        if ($user) {
            if ($course->isEnrolledBy($user->id) || $course->user_id === $user->id || $user->isAdmin()) {
                $isUserAllowed = true;
            }
        }

        if (!$isUserAllowed && !$lesson->is_preview) {
            return redirect()->route('course.show', $slug)->with('error', 'Please enroll in this course to start learning.');
        }

        $lesson->load(['comments' => fn($q) => $q->orderBy('created_at', 'desc'), 'comments.user', 'comments.replies.user']);

        $completedLessonIds = $user
            ? LessonProgress::where('user_id', $user->id)
                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                ->pluck('lesson_id')
                ->toArray()
            : [];

        $totalLessons   = $course->lessons->count();
        $completedCount = count($completedLessonIds);
        $progress       = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $discussions = \App\Models\Discussion::where('course_id', $course->id)
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('lessons.player', compact('course', 'lesson', 'completedLessonIds', 'progress', 'totalLessons', 'completedCount', 'discussions'));
    }

    /**
     * My Learning page — student's enrolled courses with progress.
     */
    public function myLearning()
    {
        $user        = auth()->user();
        $enrollments = Enrollment::with(['course.user', 'course.lessons'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        foreach ($enrollments as $enrollment) {
            $totalLessons = $enrollment->course->lessons->count();
            if ($totalLessons > 0) {
                $completedCount = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $enrollment->course->lessons->pluck('id'))
                    ->count();
                $enrollment->progress       = round(($completedCount / $totalLessons) * 100);
                $enrollment->completedCount = $completedCount;
            } else {
                $enrollment->progress       = 0;
                $enrollment->completedCount = 0;
            }
            $enrollment->totalLessons = $totalLessons;
        }

        // Determine "Continue Watching"
        $continueLesson = null;
        $continueCourse = null;

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

        $totalEnrolledCount = $enrollments->count();

        $completedCoursesCount = 0;
        foreach ($enrollments as $enrollment) {
            if ($enrollment->progress >= 100) {
                $completedCoursesCount++;
            }
        }

        $inProgressCount = $totalEnrolledCount - $completedCoursesCount;

        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        $recommended = Course::with(['category', 'user', 'reviews'])
            ->where('status', 'published')
            ->whereNotIn('id', $enrolledCourseIds)
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(4)
            ->get();

        return view('student.my-courses', compact(
            'enrollments',
            'continueLesson',
            'continueCourse',
            'totalEnrolledCount',
            'completedCoursesCount',
            'inProgressCount',
            'recommended'
        ));
    }

    /**
     * Toggle a lesson in user's playlist.
     */
    public function togglePlaylist(Lesson $lesson)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $item = \App\Models\Playlist::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if ($item) {
            $item->delete();
            $action = 'removed';
        } else {
            \App\Models\Playlist::create(['user_id' => $user->id, 'lesson_id' => $lesson->id]);
            $action = 'added';
        }

        return response()->json(['status' => 'success', 'action' => $action]);
    }

    /**
     * Toggle a lesson in user's watch later list.
     */
    public function toggleWatchLater(Lesson $lesson)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $item = \App\Models\WatchLater::where('user_id', $user->id)->where('lesson_id', $lesson->id)->first();

        if ($item) {
            $item->delete();
            $action = 'removed';
        } else {
            \App\Models\WatchLater::create(['user_id' => $user->id, 'lesson_id' => $lesson->id]);
            $action = 'added';
        }

        return response()->json(['status' => 'success', 'action' => $action]);
    }
}
