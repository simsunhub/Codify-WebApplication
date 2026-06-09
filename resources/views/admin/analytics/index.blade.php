@extends('admin.layouts.app')
@section('title', __('messages.analytics.title'))

@section('extra-css')
<style>
    /* ─── DYNAMIC DARK LUXURY THEME OVERRIDES ─── */
    body {
        background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.12), transparent 45%),
                    radial-gradient(circle at bottom left, rgba(168, 85, 247, 0.1), transparent 45%),
                    #070a13 !important;
        color: #f8fafc !important;
    }
    
    .content-shell {
        background: transparent !important;
    }
    
    .topbar {
        background: rgba(7, 10, 19, 0.6) !important;
        backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    
    .page-title {
        color: #ffffff !important;
        background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .text-muted, .text-muted * {
        color: #94a3b8 !important;
    }
    
    .search-bar {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    
    .search-bar input {
        color: #ffffff !important;
    }
    
    .search-bar input::placeholder {
        color: #64748b !important;
    }
    
    .icon-pill {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    
    .btn-light {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #f8fafc !important;
    }
    
    .dropdown-menu {
        background: #0b0f19 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
    }
    
    .dropdown-item {
        color: #cbd5e1 !important;
    }
    
    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }
    
    /* ─── ANALYTICS COMPONENT STYLES ─── */
    .analytics-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .analytics-grid-3 {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1199px) {
        .analytics-grid-2, .analytics-grid-3 {
            grid-template-columns: 1fr;
        }
    }
    
    .chart-card {
        background: rgba(13, 18, 33, 0.45) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4) !important;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), 
                    border-color 0.3s ease, 
                    box-shadow 0.3s ease;
    }
    
    .chart-card:hover {
        transform: translateY(-6px);
        border-color: rgba(99, 102, 241, 0.35) !important;
        box-shadow: 0 24px 60px rgba(99, 102, 241, 0.15) !important;
    }
    
    .chart-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .chart-header i {
        font-size: 20px;
    }
    
    .chart-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #ffffff !important;
        letter-spacing: -0.02em;
    }
    
    .chart-body {
        padding: 24px;
        flex: 1;
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="analytics-grid-2">
    <!-- Revenue Trends -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fa-solid fa-money-bill-trend-up" style="color: #fb923c !important;"></i>
            <h3 class="chart-title">{{ __('messages.analytics.revenue_trends') }}</h3>
        </div>
        <div class="chart-body">
            <div style="height: 300px; width: 100%;">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- User Growth -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fa-solid fa-users" style="color: #38bdf8 !important;"></i>
            <h3 class="chart-title">{{ __('messages.analytics.user_growth') }}</h3>
        </div>
        <div class="chart-body">
            <div style="height: 300px; width: 100%;">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="analytics-grid-3">
    <!-- Course Popularity -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fa-solid fa-graduation-cap" style="color: #34d399 !important;"></i>
            <h3 class="chart-title">{{ __('messages.analytics.top_courses') }}</h3>
        </div>
        <div class="chart-body">
            <div style="height: 280px; width: 100%;">
                <canvas id="coursePopularityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Coding Language Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fa-solid fa-code" style="color: #a78bfa !important;"></i>
            <h3 class="chart-title">{{ __('messages.analytics.lang_distribution') }}</h3>
        </div>
        <div class="chart-body">
            <div style="height: 280px; width: 100%;">
                <canvas id="languageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Coding Submissions Status -->
    <div class="chart-card">
        <div class="chart-header">
            <i class="fa-solid fa-circle-check" style="color: #fb7185 !important;"></i>
            <h3 class="chart-title">{{ __('messages.analytics.sub_status') }}</h3>
        </div>
        <div class="chart-body">
            <div style="height: 280px; width: 100%;">
                <canvas id="codingStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const fontConfig = {
        family: "'Manrope', 'Plus Jakarta Sans', sans-serif",
        size: 11,
        weight: '600'
    };

    const gridConfig = {
        color: 'rgba(255, 255, 255, 0.04)',
        borderColor: 'transparent'
    };

    const tooltipConfig = {
        backgroundColor: 'rgba(15, 23, 42, 0.95)',
        titleFont: { family: "'Manrope', sans-serif", weight: '700' },
        bodyFont: { family: "'Manrope', sans-serif" },
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 8,
        displayColors: true
    };

    // ─── 1. REVENUE TRENDS ───
    const revenueCtx = document.getElementById('revenueTrendChart').getContext('2d');
    
    // Gradients
    const revGrad = revenueCtx.createLinearGradient(0, 0, 0, 300);
    revGrad.addColorStop(0, 'rgba(249, 115, 22, 0.35)');
    revGrad.addColorStop(1, 'rgba(249, 115, 22, 0)');
    
    const feeGrad = revenueCtx.createLinearGradient(0, 0, 0, 300);
    feeGrad.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
    feeGrad.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueMonths) !!},
            datasets: [
                {
                    label: "{{ __('messages.analytics.revenue') }}",
                    data: {!! json_encode($revenueTrend) !!},
                    borderColor: '#f97316',
                    backgroundColor: revGrad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#f97316',
                    pointHoverRadius: 6
                },
                {
                    label: "{{ __('messages.analytics.platform_fee') }}",
                    data: {!! json_encode($platformShareTrend) !!},
                    borderColor: '#6366f1',
                    backgroundColor: feeGrad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: fontConfig, color: '#94a3b8' }
                },
                tooltip: tooltipConfig
            },
            scales: {
                x: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: { display: false } },
                y: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: gridConfig }
            }
        }
    });

    // ─── 2. USER GROWTH ───
    const userCtx = document.getElementById('userGrowthChart').getContext('2d');
    
    const studentGrad = userCtx.createLinearGradient(0, 0, 0, 300);
    studentGrad.addColorStop(0, '#38bdf8');
    studentGrad.addColorStop(1, '#0284c7');

    const teacherGrad = userCtx.createLinearGradient(0, 0, 0, 300);
    teacherGrad.addColorStop(0, '#c084fc');
    teacherGrad.addColorStop(1, '#6366f1');

    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($userMonths) !!},
            datasets: [
                {
                    label: "{{ __('messages.analytics.students') }}",
                    data: {!! json_encode($studentTrend) !!},
                    backgroundColor: studentGrad,
                    borderRadius: 8
                },
                {
                    label: "{{ __('messages.analytics.instructors') }}",
                    data: {!! json_encode($teacherTrend) !!},
                    backgroundColor: teacherGrad,
                    borderRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: fontConfig, color: '#94a3b8' }
                },
                tooltip: tooltipConfig
            },
            scales: {
                x: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: { display: false } },
                y: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: gridConfig }
            }
        }
    });

    // ─── 3. COURSE POPULARITY ───
    const courseCtx = document.getElementById('coursePopularityChart').getContext('2d');
    
    const courseGrad = courseCtx.createLinearGradient(0, 0, 300, 0);
    courseGrad.addColorStop(0, '#10b981');
    courseGrad.addColorStop(1, '#059669');

    new Chart(courseCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($courseChart['labels']) !!},
            datasets: [{
                label: "{{ __('messages.analytics.enrollments') }}",
                data: {!! json_encode($courseChart['data']) !!},
                backgroundColor: courseGrad,
                borderRadius: 8,
                barThickness: 18
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: tooltipConfig
            },
            scales: {
                x: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: gridConfig },
                y: { ticks: { font: fontConfig, color: '#94a3b8' }, grid: { display: false } }
            }
        }
    });

    // ─── 4. LANGUAGE DISTRIBUTION ───
    new Chart(document.getElementById('languageChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($langChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($langChart['data']) !!},
                backgroundColor: ['#6366f1', '#f59e0b', '#ef4444', '#10b981', '#a855f7', '#0ea5e9'],
                borderWidth: 1,
                borderColor: '#0f172a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: fontConfig, color: '#94a3b8', boxWidth: 12 }
                },
                tooltip: tooltipConfig
            },
            cutout: '70%'
        }
    });

    // ─── 5. CODING SUBMISSIONS STATUS ───
    new Chart(document.getElementById('codingStatusChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($codingStatusChart['labels']) !!},
            datasets: [{
                data: {!! json_encode($codingStatusChart['data']) !!},
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 1,
                borderColor: '#0f172a'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: fontConfig, color: '#94a3b8', boxWidth: 12 }
                },
                tooltip: tooltipConfig
            }
        }
    });
});
</script>
@endsection
