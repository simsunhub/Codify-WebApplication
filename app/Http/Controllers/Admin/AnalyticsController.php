<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\CodingSubmission;
use App\Models\ProgrammingLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Revenue Analytics (Monthly Revenue last 6 months)
        $revenueMonths = [];
        $revenueTrend = [];
        $platformShareTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueMonths[] = $date->format('M Y');
            
            $revenueTrend[] = OrderItem::whereHas('order', function($q) {
                    $q->where('status', 'completed');
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('price');

            $platformShareTrend[] = OrderItem::whereHas('order', function($q) {
                    $q->where('status', 'completed');
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('platform_fee');
        }

        // 2. User Growth Trend (last 6 months)
        $userMonths = [];
        $studentTrend = [];
        $teacherTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userMonths[] = $date->format('M Y');
            
            $studentTrend[] = User::where('role', 'student')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $teacherTrend[] = User::where('role', 'instructor')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // 3. Course Analytics (Top 5 popular courses by enrollment count)
        $topCourses = Course::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();

        $courseChart = [
            'labels' => $topCourses->pluck('title')->toArray(),
            'data' => $topCourses->pluck('enrollments_count')->toArray()
        ];

        // 4. Coding Analytics (Submissions by language & status)
        $languages = ProgrammingLanguage::withCount('submissions')->get();
        $langChart = [
            'labels' => $languages->pluck('name')->toArray(),
            'data' => $languages->pluck('submissions_count')->toArray()
        ];

        $acceptedCount = CodingSubmission::where('status', 'accepted')->count();
        $failedCount = CodingSubmission::where('status', '!=', 'accepted')->count();

        $codingStatusChart = [
            'labels' => [__('messages.analytics.accepted'), __('messages.analytics.failed')],
            'data' => [$acceptedCount, $failedCount]
        ];

        return view('admin.analytics.index', compact(
            'revenueMonths', 'revenueTrend', 'platformShareTrend',
            'userMonths', 'studentTrend', 'teacherTrend',
            'courseChart', 'langChart', 'codingStatusChart'
        ));
    }
}