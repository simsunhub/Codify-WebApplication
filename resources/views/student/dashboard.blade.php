@extends('layouts.app')

@section('extra-css')
<style>
    :root {
        /* Override student primary theme color to blue */
        --student-primary: #3B82F6;
        --student-light: #EFF6FF;
        --student-dark: #1D4ED8;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: var(--transition);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: rgba(59, 130, 246, 0.25);
    }
    .stat-icon {
        width: 56px; height: 56px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
    }
    .stat-info h4 {
        font-size: 13px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .stat-info div {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* Continue watching */
    .continue-watch-box {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: var(--radius-lg);
        color: #fff;
        padding: 30px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
    }
    .continue-watch-box::before {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        top: -150px; right: -50px;
    }
    .continue-watch-details {
        max-width: 60%;
        position: relative;
        z-index: 2;
    }
    .continue-tag {
        background: rgba(255,255,255,0.15);
        padding: 4px 12px;
        border-radius: var(--radius-full);
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        margin-bottom: 12px;
    }
    .continue-watch-details h3 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1.2;
    }
    .continue-watch-details p {
        font-size: 14px;
        color: rgba(255,255,255,0.85);
        margin-bottom: 20px;
    }

    .continue-watch-btn {
        background: #fff;
        color: var(--student-dark);
        font-weight: 700;
        padding: 12px 28px;
        border-radius: var(--radius-md);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .continue-watch-btn:hover {
        background: var(--student-light);
        transform: translateX(4px);
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Course Grid */
    .course-card-custom {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: var(--transition);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .course-card-custom:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.15);
        border-color: rgba(99, 102, 241, 0.3);
    }
    .course-card-img {
        height: 160px;
        width: 100%;
        object-fit: cover;
    }
    .course-card-body {
        padding: 20px;
    }
    .course-card-category {
        font-size: 11px;
        font-weight: 700;
        color: var(--student-primary);
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .course-card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
        line-height: 1.4;
        height: 42px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .course-progress-container {
        margin-top: 15px;
    }
    .course-progress-header {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    @media(max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr; }
        .continue-watch-box { flex-direction: column; text-align: center; gap: 20px; }
        .continue-watch-details { max-width: 100%; }
    }

    /* Premium Announcements box for students */
    .announcement-box {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.07) 0%, rgba(255, 255, 255, 0.01) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        box-shadow: 0 8px 32px 0 rgba(59, 130, 246, 0.05);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 30px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .bell-glow-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.15);
        color: var(--student-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        position: relative;
    }
    .bell-glow-icon::after {
        content: '';
        position: absolute;
        width: 8px; height: 8px;
        background: #ef4444;
        border-radius: 50%;
        top: 2px; right: 2px;
        border: 2px solid #1a1a2e;
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
        border-color: rgba(59, 130, 246, 0.15);
    }
    .ann-bullet {
        display: block;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--student-primary);
        margin-top: 6px;
        box-shadow: 0 0 8px var(--student-primary);
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
        color: var(--text-muted);
    }
    .ann-item-desc {
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.45;
    }
    .ann-course-badge {
        font-size: 11px;
        font-weight: 700;
        color: var(--student-primary);
        background: rgba(59, 130, 246, 0.1);
        padding: 2px 8px;
        border-radius: 4px;
        margin-left: 8px;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1280px; margin: 0 auto; padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <!-- Announcements -->
    @if(isset($announcements) && $announcements->isNotEmpty())
        <div class="announcement-box" style="margin-top: 25px;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="bell-glow-icon">
                    <i class="fa-solid fa-bell animate-swing"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-bold" style="font-size: 15px; color: var(--text-primary);">
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
                                <span class="ann-item-title">
                                    {{ $announcement->title }}
                                    @if($announcement->target_role == 'course_students' && $announcement->course)
                                        <span class="ann-course-badge">
                                            <i class="fa-solid fa-graduation-cap me-1"></i>
                                            {{ $announcement->course->title }}
                                        </span>
                                    @endif
                                </span>
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

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.12);"><i class="fas fa-graduation-cap"></i></div>
            <div class="stat-info">
                <h4>{{ __('messages.dash.my_courses') }}</h4>
                <div>{{ $totalEnrolledCount }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="color: #f59e0b; background: rgba(245, 158, 11, 0.12);"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <h4>{{ __('messages.dash.active_courses_lbl') }}</h4>
                <div>{{ $inProgressCount }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.12);"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h4>{{ __('messages.course.completed') }}</h4>
                <div>{{ $completedCoursesCount }}</div>
            </div>
        </div>
    </div>

    <!-- Continue Watching Section -->
    @if($continueLesson && $continueCourse)
        <div class="continue-watch-box">
            <div class="continue-watch-details">
                <span class="continue-tag">{{ __('messages.dash.continue_learning') }}</span>
                <h3>{{ $continueCourse->title }}</h3>
                <p>{{ __('messages.lesson.description') }}: <strong>{{ $continueLesson->title }}</strong></p>
                <a href="{{ route('course.learn', ['slug' => $continueCourse->slug, 'lesson' => $continueLesson->id]) }}" class="continue-watch-btn">
                    <i class="fas fa-play"></i> {{ __('messages.learning.continue') }}
                </a>
            </div>
            
            <div style="width: 260px; height: 150px; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.2); flex-shrink: 0; position: relative; z-index: 2;">
                <img src="{{ $continueCourse->image ? asset('storage/' . $continueCourse->image) : asset('images/course-placeholder.jpg') }}" 
                     alt="{{ $continueCourse->title }}" 
                     style="width: 100%; height: 100%; object-fit: cover;"
                     onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&auto=format&fit=crop&q=80'">
            </div>
        </div>
    @endif

    <!-- Recently Enrolled Courses -->
    <div style="margin-bottom: 40px;">
        <h3 class="section-title">
            <i class="fas fa-history" style="color: var(--student-primary);"></i> {{ __('messages.learning.title') }}
        </h3>
        
        @if($enrollments->count() > 0)
            <div class="grid-3">
                @foreach($enrollments->take(3) as $enrollment)
                    <div class="course-card-custom">
                        <img src="{{ $enrollment->course->image ? asset('storage/' . $enrollment->course->image) : asset('images/course-placeholder.jpg') }}" 
                             alt="{{ $enrollment->course->title }}" 
                             class="course-card-img"
                             onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&auto=format&fit=crop&q=80'">
                        <div class="course-card-body">
                            <div class="course-card-category">{{ $enrollment->course->category->name }}</div>
                            <h4 class="course-card-title">{{ $enrollment->course->title }}</h4>
                            
                            <div class="course-progress-container">
                                <div class="course-progress-header">
                                    <span>{{ __('messages.learning.progress') }}</span>
                                    <strong>{{ $enrollment->progress }}%</strong>
                                </div>
                                <div class="progress-wrap">
                                    <div class="progress-bar" style="width: {{ $enrollment->progress }}%; background: var(--student-primary);"></div>
                                </div>
                            </div>
                            
                            <a href="{{ route('course.learn', $enrollment->course->slug) }}" class="btn btn-outline btn-sm btn-full" style="margin-top: 15px; border-color: var(--student-primary); color: var(--student-primary);">
                                <i class="fas fa-sign-in-alt"></i> {{ __('messages.course.go_to_course') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="glass-card" style="padding: 40px; text-align: center;">
                <i class="fas fa-graduation-cap" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
                <h4 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">{{ __('messages.learning.no_courses') }}</h4>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">{{ __('messages.home.section_subtitle') }}</p>
                <a href="{{ route('search') }}?q=" class="btn btn-gradient" style="border-radius: 10px;">
                    {{ __('messages.learning.browse') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Recommended Courses -->
    <div>
         <h3 class="section-title">
             <i class="fas fa-thumbs-up" style="color: var(--student-primary);"></i> {{ __('messages.home.popular_this_week') }}
         </h3>
         
         @if($recommended->count() > 0)
             <div class="grid-4">
                 @foreach($recommended as $rcourse)
                     <div class="course-card-custom">
                         <img src="{{ $rcourse->image ? asset('storage/' . $rcourse->image) : asset('images/course-placeholder.jpg') }}" 
                              alt="{{ $rcourse->title }}" 
                              class="course-card-img"
                              onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&auto=format&fit=crop&q=80'">
                         <div class="course-card-body">
                             <div class="course-card-category">{{ $rcourse->category->name }}</div>
                             <h4 class="course-card-title">{{ $rcourse->title }}</h4>
                             
                             <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                                 <div style="display: flex; align-items: center; gap: 4px;">
                                     <i class="fas fa-star" style="color: var(--star); font-size: 13px;"></i>
                                     <span style="font-size: 13px; font-weight: 700; color: var(--text-primary);">{{ number_format($rcourse->average_rating, 1) }}</span>
                                     <span style="font-size: 11px; color: var(--text-muted);">({{ $rcourse->reviews_count }})</span>
                                 </div>
                                 <span style="font-size: 15px; font-weight: 800; color: var(--brand);">
                                     @if($rcourse->price > 0)
                                         ${{ number_format($rcourse->price, 2) }}
                                     @else
                                         {{ __('messages.course.free') }}
                                     @endif
                                 </span>
                             </div>
                             
                             <a href="{{ route('course.show', $rcourse->slug) }}" class="btn btn-primary btn-sm btn-full" style="background: var(--student-primary); border-color: var(--student-primary);">
                                 {{ __('messages.home.browse_courses') }}
                             </a>
                         </div>
                     </div>
                 @endforeach
             </div>
         @else
             <div class="card" style="padding: 40px; text-align: center; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: var(--radius-lg);">
                 <i class="fas fa-check-circle" style="font-size: 44px; color: var(--success); margin-bottom: 16px;"></i>
                 <h4 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">{{ __('messages.dash.no_notifications') }}</h4>
             </div>
         @endif
     </div>

</div>
@endsection