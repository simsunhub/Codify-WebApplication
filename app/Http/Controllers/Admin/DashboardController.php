<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $totalEnrollments = Enrollment::count();
        $totalRevenue = Course::join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->sum('courses.price');
        $totalReviews = Review::count();
        $unreadContacts = Contact::where('is_read', false)->count();

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get();

        // Recent reviews
        $recentReviews = Review::with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalStudents', 'totalTeachers',
            'totalCourses', 'activeCourses', 'totalEnrollments',
            'totalRevenue', 'totalReviews', 'unreadContacts',
            'recentEnrollments', 'recentReviews'
        ));
    }
}
