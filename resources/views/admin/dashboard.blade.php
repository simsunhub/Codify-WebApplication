@extends('admin.layouts.app')
@section('title', __('messages.dash.dashboard'))

@section('extra-css')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Premium Styling Overrides */
    body {
        font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(24px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.75s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }

    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #311042 100%);
        border-radius: 24px;
        padding: 36px 40px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        margin-bottom: 32px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.25) 0%, transparent 70%);
        filter: blur(40px);
        pointer-events: none;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 10%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, transparent 70%);
        filter: blur(35px);
        pointer-events: none;
    }

    .welcome-banner-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .welcome-banner-text {
        color: rgba(241, 245, 249, 0.8);
        font-size: 15px;
        max-width: 650px;
        line-height: 1.6;
    }

    /* Stats Grid Layout */
    .stats-grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 1200px) {
        .stats-grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stats-grid-container {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    /* Live Stat Cards */
    .dashboard-stat-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dashboard-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        border-color: rgba(249, 115, 22, 0.2);
    }

    .dashboard-stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #ffffff;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .dashboard-stat-card:hover .dashboard-stat-icon-wrapper {
        transform: rotate(5deg) scale(1.08);
    }

    .icon-grad-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 8px 16px rgba(249, 115, 22, 0.2); }
    .icon-grad-blue { background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%); box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2); }
    .icon-grad-emerald { background: linear-gradient(135deg, #34d399 0%, #059669 100%); box-shadow: 0 8px 16px rgba(5, 150, 105, 0.2); }
    .icon-grad-violet { background: linear-gradient(135deg, #a78bfa 0%, #6366f1 100%); box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2); }

    .stat-val {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .stat-lbl {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-top: 5px;
    }

    /* Cards */
    .premium-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.02);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .premium-card:hover {
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.04);
    }

    .premium-card-header {
        padding: 24px 28px;
        background: transparent;
        border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .premium-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-card-body {
        padding: 28px;
    }

    /* Grid Layouts */
    .premium-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    @media (max-width: 992px) {
        .premium-grid-2 {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Custom tables */
    .premium-table {
        width: 100%;
        border-collapse: collapse;
    }

    .premium-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        padding: 16px 24px;
        border-bottom: 1.5px solid rgba(15, 23, 42, 0.05);
    }

    .premium-table td {
        padding: 18px 24px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.04);
        color: #0f172a;
        vertical-align: middle;
    }

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tr {
        transition: background-color 0.2s ease;
    }

    .premium-table tr:hover {
        background-color: rgba(249, 115, 22, 0.015);
    }

    /* Reviews items styling */
    .review-item-box {
        display: flex;
        gap: 16px;
        padding: 18px 0;
        border-bottom: 1px solid rgba(15, 23, 42, 0.04);
        transition: all 0.2s ease;
    }

    .review-item-box:last-child {
        border-bottom: none;
    }

    .review-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.15);
    }
</style>
@endsection

@section('content')
<!-- Welcome Banner Section -->
<div class="welcome-banner animate-fade-in">
    <div class="welcome-banner-title">
        {{ __('messages.dash.welcome_title', ['name' => auth()->user()->name ?? 'Admin']) }}
    </div>
    <p class="welcome-banner-text">
        {{ __('messages.dash.welcome_subtitle') }}
    </p>
</div>

<!-- Stats Grid Section -->
<div class="stats-grid-container animate-fade-in delay-1">
    <!-- Total Users -->
    <div class="dashboard-stat-card">
        <div class="dashboard-stat-icon-wrapper icon-grad-orange">
            <i class="fa-solid fa-users"></i>
        </div>
        <div>
            <div class="stat-val">{{ number_format($totalUsers) }}</div>
            <div class="stat-lbl">{{ __('messages.dash.total_users') }}</div>
        </div>
    </div>

    <!-- Active Courses -->
    <div class="dashboard-stat-card">
        <div class="dashboard-stat-icon-wrapper icon-grad-blue">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <div>
            <div class="stat-val">{{ number_format($activeCourses) }}/{{ number_format($totalCourses) }}</div>
            <div class="stat-lbl">{{ __('messages.dash.active_courses') }}</div>
        </div>
    </div>

    <!-- Total Enrollments -->
    <div class="dashboard-stat-card">
        <div class="dashboard-stat-icon-wrapper icon-grad-emerald">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div>
            <div class="stat-val">{{ number_format($totalEnrollments) }}</div>
            <div class="stat-lbl">{{ __('messages.dash.total_enrollments') }}</div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="dashboard-stat-card">
        <div class="dashboard-stat-icon-wrapper icon-grad-violet">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div>
            <div class="stat-val">${{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-lbl">{{ __('messages.dash.total_revenue') }}</div>
        </div>
    </div>
</div>

<div class="premium-grid-2 animate-fade-in delay-2">
    <!-- Recent Enrollments Card -->
    <div class="premium-card">
        <div class="premium-card-header">
            <h3 class="premium-card-title">
                <i class="fa-solid fa-clipboard-list text-primary" style="color: var(--brand) !important;"></i>
                {{ __('messages.dash.recent_enrollments') }}
            </h3>
        </div>
        <div class="premium-card-body p-0">
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.dash.user') }}</th>
                            <th>{{ __('messages.dash.course') }}</th>
                            <th>{{ __('messages.dash.price') }}</th>
                            <th>{{ __('messages.dash.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $enrollment->user->name ?? 'Guest' }}</div>
                                    <small class="text-muted">{{ $enrollment->user->email ?? '' }}</small>
                                </td>
                                <td>{{ $enrollment->course->title ?? '' }}</td>
                                <td>
                                    @if(($enrollment->course->price ?? 0) > 0)
                                        <span class="price-pill">${{ number_format($enrollment->course->price, 2) }}</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-pill fw-bold">{{ __('messages.course.free') }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $enrollment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.dash.no_enrollments') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Reviews Card -->
    <div class="premium-card">
        <div class="premium-card-header">
            <h3 class="premium-card-title">
                <i class="fa-solid fa-star text-warning"></i>
                {{ __('messages.dash.recent_reviews') }}
            </h3>
        </div>
        <div class="premium-card-body">
            @forelse($recentReviews as $review)
                <div class="review-item-box">
                    <div class="review-avatar">
                        {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold">{{ $review->user->name ?? 'User' }}</span>
                            <span class="text-warning font-size-12">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star" style="{{ $i <= $review->rating ? '' : 'opacity:0.25;' }}"></i>
                                @endfor
                            </span>
                        </div>
                        <div class="text-muted small mb-2">{{ $review->course->title ?? '' }}</div>
                        <p class="mb-0 text-secondary" style="font-size:13.5px;">{{ $review->comment }}</p>
                        <small class="text-muted d-block mt-2" style="font-size:11px;">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">{{ __('messages.dash.no_reviews') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
