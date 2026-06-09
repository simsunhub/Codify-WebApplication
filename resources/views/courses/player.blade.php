@extends('layouts.app')

@section('title', $activeLesson->title . ' - ' . $course->title)
@section('page-title', __('Training'))

@section('extra-css')
<style>
.player-layout {
    display: flex;
    gap: 24px;
    height: calc(100vh - 120px);
    overflow: hidden;
}

/* Left: Video Player Area */
.player-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.video-wrapper {
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
    border-radius: var(--radius-lg);
    overflow: hidden;
    position: relative;
    box-shadow: var(--shadow-lg);
}
.video-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    background: linear-gradient(135deg, #1f2937, #111827);
}
.video-placeholder i {
    font-size: 64px;
    color: rgba(255,255,255,0.2);
    margin-bottom: 16px;
}
.player-content {
    margin-top: 24px;
    padding: 24px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow-y: auto;
}
.lesson-title {
    font-size: 24px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 16px;
}
.lesson-desc {
    font-size: 15px;
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Right: Playlist Sidebar */
.player-sidebar {
    width: 380px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}
.playlist-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-light);
}
.playlist-course-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
    line-height: 1.3;
}
.playlist-progress-text {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.playlist-progress-bar {
    height: 4px;
    background: var(--border-light);
    border-radius: 2px;
}
.playlist-progress-fill {
    height: 100%;
    background: var(--brand);
    border-radius: 2px;
    width: 10%; /* hardcoded for MVP */
}
.playlist-items {
    flex: 1;
    overflow-y: auto;
}
.playlist-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-light);
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition);
}
.playlist-item:hover {
    background: var(--bg-secondary);
}
.playlist-item.active {
    background: rgba(255,107,53,0.05);
    border-left: 3px solid var(--brand);
}
.playlist-item-num {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    width: 20px;
}
.playlist-item-icon {
    font-size: 14px;
    color: var(--text-muted);
    margin-top: 2px;
}
.playlist-item.active .playlist-item-icon {
    color: var(--brand);
}
.playlist-item-content {
    flex: 1;
}
.playlist-item-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
}
.playlist-item.active .playlist-item-title {
    color: var(--brand);
}

@media (max-width: 1024px) {
    .player-layout {
        flex-direction: column;
        height: auto;
        overflow: visible;
    }
    .player-sidebar {
        width: 100%;
        height: 500px; /* Fixed height for scrollable playlist on mobile */
    }
}
</style>
@endsection

@section('content')
<div class="player-layout">
    
    <div class="player-sidebar">
        <div class="playlist-header">
            <div class="playlist-course-title">{{ $course->title }}</div>
            <div class="playlist-progress-text">{{ __('Lessons') }}: {{ $course->lessons->count() }}</div>
            <div class="playlist-progress-bar">
                <div class="playlist-progress-fill"></div>
            </div>
        </div>
        <div class="playlist-items">
            @foreach($course->lessons as $lesson)
            <a href="{{ route('course.learn', ['slug' => $course->slug, 'lesson' => $lesson->id]) }}" class="playlist-item {{ $activeLesson->id === $lesson->id ? 'active' : '' }}">
                <div class="playlist-item-num">{{ $loop->iteration }}</div>
                <div class="playlist-item-icon">
                    @if($lesson->video_url)
                        <i class="fas fa-play-circle"></i>
                    @else
                        <i class="fas fa-file-alt"></i>
                    @endif
                </div>
                <div class="playlist-item-content">
                    <div class="playlist-item-title">{{ $lesson->title }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection