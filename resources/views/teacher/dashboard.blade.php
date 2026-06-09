@extends('teacher.layouts.app')

@section('title', __('messages.dash.dashboard'))
@section('breadcrumb', __('messages.dash.dashboard'))

@section('page-actions')
<a href="{{ route('teacher.courses.create') }}" class="ed-btn ed-btn-primary">
    <i class="fa-solid fa-plus"></i> Add Course
</a>
@endsection

@section('extra-css')
<style>
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .delay-1 { animation-delay: 0.05s; }
    .delay-2 { animation-delay: 0.1s; }
    .delay-3 { animation-delay: 0.15s; }
    .delay-4 { animation-delay: 0.2s; }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(20, 20, 20, 0.6) 100%);
        border-radius: 20px;
        padding: 32px 40px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
        border: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .welcome-banner h1 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 8px;
        color: #fff;
    }
    .welcome-banner p {
        font-size: 14px;
        color: var(--text-muted);
        max-width: 600px;
    }
    .welcome-banner-visual {
        font-size: 72px;
        color: rgba(99, 102, 241, 0.12);
        position: absolute;
        right: 40px;
        pointer-events: none;
    }
    
    /* Stats Card Custom Styling */
    .t-kpi-card {
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        height: 100%;
        border-radius: 18px;
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        transition: var(--transition);
    }
    .t-kpi-card:hover {
        transform: translateY(-3px);
        border-color: rgba(99, 102, 241, 0.25);
        box-shadow: var(--card-shadow-h);
    }
    .t-kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .t-kpi-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--text-muted);
        margin-bottom: 4px;
    }
    .t-kpi-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
    }
    .t-kpi-sub {
        font-size: 12px;
        color: var(--text-dim);
        margin-top: 4px;
    }

    /* Course performance table & Recent Enrollments Custom styling */
    .table-responsive {
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.05) transparent;
    }
    
    /* Announcement Widget */
    .announcement-box {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.07) 0%, rgba(255, 255, 255, 0.01) 100%);
        border: 1px solid rgba(99, 102, 241, 0.2);
        box-shadow: 0 8px 32px 0 rgba(99, 102, 241, 0.05);
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 30px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .bell-glow-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(99, 102, 241, 0.15);
        color: var(--brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        position: relative;
    }
    .bell-glow-icon::after {
        content: '';
        position: absolute;
        width: 8px; height: 8px;
        background: var(--red);
        border-radius: 50%;
        top: 2px; right: 2px;
        border: 2px solid #000;
    }
    @keyframes swing {
        0%, 100% { transform: rotate(0deg); }
        20% { transform: rotate(15deg); }
        40% { transform: rotate(-10deg); }
        60% { transform: rotate(5deg); }
        80% { transform: rotate(-5deg); }
    }
    .animate-swing {
        animation: swing 2.5s ease infinite;
        transform-origin: top center;
    }
    .ann-item-row {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid rgba(255, 255, 255, 0.03);
        transition: var(--transition);
    }
    .ann-item-row:hover {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(99, 102, 241, 0.15);
    }
    .ann-bullet {
        display: block;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--brand);
        margin-top: 6px;
        box-shadow: 0 0 8px var(--brand);
    }
    .ann-item-content {
        flex: 1;
        min-width: 0;
    }
    .ann-item-title {
        font-weight: 700;
        font-size: 14px;
        color: #fff;
    }
    .ann-item-date {
        font-size: 11px;
        color: var(--text-dim);
    }
    .ann-item-desc {
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.45;
    }
</style>
@endsection

@section('content')
{{-- Welcome Banner --}}
<div class="welcome-banner animate-fade-in delay-1">
    <div>
        <h1>{{ __('messages.dash.welcome_title', ['name' => $__user->name ?? 'Instructor']) }}</h1>
        <p class="mb-0">{{ __('messages.dash.welcome_subtitle') }}</p>
    </div>
    <div class="welcome-banner-visual">
        <i class="fa-solid fa-graduation-cap"></i>
    </div>
</div>

{{-- Active Announcements --}}
@if($announcements->isNotEmpty())
<div class="announcement-box animate-fade-in delay-2">
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="bell-glow-icon">
            <i class="fa-solid fa-bell animate-swing"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold" style="font-size: 15px; color: var(--text);">
                {{ __('messages.announcements.announcements') }}
            </h5>
            <p class="m-0 text-muted" style="font-size: 11px;">{{ __('messages.announcements.latest_updates') }}</p>
        </div>
    </div>
    <div class="d-flex flex-column gap-3">
        @foreach($announcements as $announcement)
            <div class="ann-item-row">
                <div class="ann-item-left">
                    <span class="ann-bullet"></span>
                </div>
                <div class="ann-item-content">
                    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
                        <span class="ann-item-title">{{ $announcement->title }}</span>
                        <span class="ann-item-date"><i class="fa-regular fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="ann-item-desc">
                        {{ $announcement->content }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- KPI Stats Row --}}
<div class="row g-4 mb-4 animate-fade-in delay-2">
    {{-- Total Courses --}}
    <div class="col-sm-6 col-xl-3">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--brand);">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('messages.dash.my_courses') }}</div>
                <div class="t-kpi-value t-kpi-val" data-count="{{ $totalCourses }}">0</div>
                <div class="t-kpi-sub">{{ $activeCourses }} {{ __('messages.dash.active_courses') }}</div>
            </div>
        </div>
    </div>
    {{-- Total Students --}}
    <div class="col-sm-6 col-xl-3">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--green);">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('messages.dash.students_list') }}</div>
                <div class="t-kpi-value t-kpi-val" data-count="{{ $totalStudents }}">0</div>
                <div class="t-kpi-sub">{{ $totalEnrollments }} {{ __('messages.dash.total_enrollments') }}</div>
            </div>
        </div>
    </div>
    {{-- Average Rating --}}
    <div class="col-sm-6 col-xl-3">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--yellow);">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('messages.dash.reviews') }}</div>
                <div class="t-kpi-value">{{ number_format($averageRating, 1) ?? '0.0' }}</div>
                <div class="t-kpi-sub">{{ $totalReviews }} {{ __('messages.dash.reviews') }}</div>
            </div>
        </div>
    </div>
    {{-- Total Revenue --}}
    <div class="col-sm-6 col-xl-3">
        <div class="t-kpi-card">
            <div class="t-kpi-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--blue);">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <div class="t-kpi-title">{{ __('messages.dash.total_revenue') }}</div>
                <div class="t-kpi-value">${{ number_format($totalRevenue, 2) }}</div>
                <div class="t-kpi-sub">{{ __('messages.analytics.revenue') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Analytics Charts Row --}}
<div class="row g-4 mb-4 animate-fade-in delay-3">
    {{-- Enrollment Trend Chart (Line) --}}
    <div class="col-lg-8">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-chart-line me-2" style="color: var(--brand);"></i>
                        {{ __('messages.analytics.revenue_trends') }}
                    </div>
                    <div class="ed-card-subtitle">{{ __('messages.analytics.user_growth') }}</div>
                </div>
            </div>
            <div class="ed-card-body d-flex flex-column justify-content-center" style="height: 320px;">
                <canvas id="enrollmentsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Students by Course Chart (Doughnut) --}}
    <div class="col-lg-4">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-chart-pie me-2" style="color: var(--brand);"></i>
                        {{ __('messages.analytics.top_courses') }}
                    </div>
                    <div class="ed-card-subtitle">{{ __('messages.analytics.lang_distribution') }}</div>
                </div>
            </div>
            <div class="ed-card-body d-flex flex-column justify-content-center" style="height: 320px; position: relative; padding: 16px;">
                <canvas id="coursesChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Detailed Tables Row --}}
<div class="row g-4 animate-fade-in delay-4">
    {{-- Course Performance Table --}}
    <div class="col-lg-8">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-table-list me-2" style="color: var(--brand);"></i>
                        {{ __('messages.dash.my_courses') }}
                    </div>
                    <div class="ed-card-subtitle">Performance stats of all courses</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table ed-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.dash.course') }}</th>
                            <th class="text-center">{{ __('messages.dash.quizzes') }}</th>
                            <th class="text-center">{{ __('messages.dash.students_list') }}</th>
                            <th class="text-center">{{ __('messages.dash.reviews') }}</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; font-size: 13.5px; color: var(--text);">
                                        {{ Str::limit($course->title, 35) }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-muted);">
                                        ${{ number_format($course->price, 2) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="ed-badge ed-badge-gray">
                                        {{ $course->lessons_count }} Lessons
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="ed-badge ed-badge-green">
                                        {{ $course->enrollments_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="ed-badge ed-badge-yellow">
                                        <i class="fa-solid fa-star me-1" style="font-size: 10px;"></i>
                                        {{ number_format($course->reviews_avg_rating ?? 0, 1) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($course->status === 'published')
                                        <span class="ed-badge ed-badge-green">
                                            {{ __('messages.dash.active_status') }}
                                        </span>
                                    @else
                                        <span class="ed-badge ed-badge-yellow">
                                            {{ __('messages.dash.disabled_status') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No courses found. Create one above!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Enrollments List --}}
    <div class="col-lg-4">
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-history me-2" style="color: var(--brand);"></i>
                        {{ __('messages.dash.recent_enrollments') }}
                    </div>
                    <div class="ed-card-subtitle">Latest additions to your courses</div>
                </div>
            </div>
            <div class="ed-card-body" style="padding: 16px; max-height: 400px; overflow-y: auto; scrollbar-width: thin;">
                @forelse($recentEnrollments as $enrollment)
                    <div class="d-flex align-items-center justify-content-between p-2 border-bottom border-secondary-subtle last-border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="ed-user-avatar-sm" style="width: 32px; height: 32px; font-size: 12px;">
                                {{ strtoupper(substr($enrollment->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size: 13.5px; font-weight: 600; color: #fff;">
                                    {{ $enrollment->user->name ?? 'Student' }}
                                </div>
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    {{ Str::limit($enrollment->course->title ?? '', 25) }}
                                </div>
                            </div>
                        </div>
                        <span style="font-size: 11px; color: var(--text-dim);">
                            {{ $enrollment->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        No recent enrollments found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    /* ── Chart.js global defaults — Modern Minimal ── */
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94a3b8'; // text-muted

    // ── Enrollments Line Chart ───────────────────────────
    const ctxLine = document.getElementById('enrollmentsChart').getContext('2d');
    const gradLine = ctxLine.createLinearGradient(0, 0, 0, 250);
    gradLine.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
    gradLine.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($enrollmentsChart['labels']) !!},
            datasets: [{
                label: '{{ __('messages.dash.total_enrollments') }}',
                data: {!! json_encode($enrollmentsChart['data']) !!},
                borderColor: '#6366f1',
                backgroundColor: gradLine,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#090d16',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111111',
                    titleColor: '#ffffff',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.05)' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
        }
    });

    // ── Students by Course Doughnut Chart ────────────────
    const ctxDoughnut = document.getElementById('coursesChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($coursesChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($coursesChart['data']) !!},
                backgroundColor: ['#6366f1', '#4f46e5', '#F59E0B', '#10B981', '#3B82F6', '#4f46e5'],
                borderWidth: 2,
                borderColor: '#090d16',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 14,
                        color: '#94a3b8',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: '#111111',
                    titleColor: '#ffffff',
                    bodyColor: '#cbd5e1',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 10
                }
            },
            cutout: '72%'
        }
    });

    // ── Animated Counters ────────────────────────────────
    document.querySelectorAll('.t-kpi-val[data-count]').forEach(el => {
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (isNaN(target) || target === 0) {
            el.textContent = '0';
            return;
        }
        const step = target / 50; // 50 ticks
        let cur = 0;
        const tick = () => {
            cur = Math.min(cur + step, target);
            el.textContent = Math.floor(cur).toLocaleString();
            if (cur < target) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
});
</script>
@endsection
