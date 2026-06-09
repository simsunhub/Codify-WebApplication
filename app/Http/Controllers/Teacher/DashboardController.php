<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\Announcement;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalCourses = Course::where('instructor_id', $user->id)->count();
        $activeCourses = Course::where('instructor_id', $user->id)->where('status', 'published')->count();

        $courseIds = Course::where('instructor_id', $user->id)->pluck('id');

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)->distinct('user_id')->count('user_id');
        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
        $totalRevenue = Course::whereIn('courses.id', $courseIds)
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->sum('courses.price');
        $averageRating = Review::whereIn('course_id', $courseIds)->avg('rating');
        $totalReviews = Review::whereIn('course_id', $courseIds)->count();

        // Recent enrollments in teacher's courses
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereIn('course_id', $courseIds)
            ->latest()
            ->take(10)
            ->get();

        // Course performance table
        $courses = Course::withCount(['enrollments', 'reviews', 'lessons'])
            ->withAvg('reviews', 'rating')
            ->where('instructor_id', $user->id)
            ->get();

        // 1. Enrollment trend (last 6 months)
        $months = [];
        $enrollmentTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $this->translateMonth($date->format('F'));
            $months[] = $monthName;
            $enrollmentTrend[] = Enrollment::whereIn('course_id', $courseIds)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }
        $enrollmentsChart = [
            'labels' => $months,
            'data' => $enrollmentTrend
        ];

        // 2. Students Distribution by Course (Doughnut)
        $coursesChart = [
            'labels' => $courses->pluck('title')->map(fn($t) => Str::limit($t, 20))->toArray(),
            'data' => $courses->pluck('enrollments_count')->toArray()
        ];

        // Active announcements targeted at teachers (all or teacher_only)
        $announcements = Announcement::forTeachers()->latest()->take(5)->get();

        return view('teacher.dashboard', compact(
            'totalCourses', 'activeCourses', 'totalStudents',
            'totalEnrollments', 'totalRevenue', 'averageRating',
            'totalReviews', 'recentEnrollments', 'courses',
            'enrollmentsChart', 'coursesChart', 'announcements'
        ));
    }

    private function translateMonth($month)
    {
        $locale = app()->getLocale();
        if ($locale === 'tr') {
            $translations = [
                'January'   => 'Oca',
                'February'  => 'Şub',
                'March'     => 'Mar',
                'April'     => 'Nis',
                'May'       => 'May',
                'June'      => 'Haz',
                'July'      => 'Tem',
                'August'    => 'Ağu',
                'September' => 'Eyl',
                'October'   => 'Eki',
                'November'  => 'Kas',
                'December'  => 'Ara',
            ];
            return $translations[$month] ?? substr($month, 0, 3);
        }

        $translations = [
            'January'   => 'Jan',
            'February'  => 'Feb',
            'March'     => 'Mar',
            'April'     => 'Apr',
            'May'       => 'May',
            'June'      => 'Jun',
            'July'      => 'Jul',
            'August'    => 'Aug',
            'September' => 'Sep',
            'October'   => 'Oct',
            'November'  => 'Nov',
            'December'  => 'Dec',
        ];

        return $translations[$month] ?? substr($month, 0, 3);
    }
}