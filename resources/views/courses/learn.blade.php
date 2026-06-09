@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)
@section('page-title', $course->title)

@section('extra-css')
<style>
    /* Override layout for full-width player */
    .page-content { padding: 0 !important; }

    /* ─── VIDEO AREA ─── */
    .video-player {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 */
        background: #111;
    }
    .video-player video,
    .video-player iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: none;
        z-index: 10;
        pointer-events: auto !important;
    }
    .video-placeholder {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,0.5);
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    }
    .video-placeholder i { font-size: 64px; margin-bottom: 16px; }

    /* Sidebar Scrollbar */
    .lesson-list::-webkit-scrollbar { width: 4px; }
    .lesson-list::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }

    /* ─── AI CHAT STYLES ─── */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    /* Markdown rendering styles */
    .ai-markdown-content p {
        margin-bottom: 0.75rem;
    }
    .ai-markdown-content p:last-child {
        margin-bottom: 0;
    }
    .ai-markdown-content pre {
        background: rgba(0, 0, 0, 0.5) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        padding: 12px !important;
        border-radius: 8px !important;
        margin: 10px 0 !important;
        overflow-x: auto !important;
    }
    .ai-markdown-content code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
        font-size: 12.5px !important;
        color: #e2e8f0 !important;
        background: transparent !important;
        padding: 0 !important;
    }
    /* Inline code style */
    .ai-markdown-content :not(pre) > code {
        background: rgba(99, 102, 241, 0.15) !important;
        color: #a5b4fc !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
    }
    .ai-markdown-content ul, 
    .ai-markdown-content ol {
        margin-bottom: 0.75rem;
        padding-left: 1.25rem;
    }
    .ai-markdown-content ul {
        list-style-type: disc;
    }
    .ai-markdown-content ol {
        list-style-type: decimal;
    }
    .ai-markdown-content li {
        margin-bottom: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full" style="margin-top: 100px;">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Левая колонка (2/3 ширины) -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            <!-- Видео-плеер -->
            <div class="video-player rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/5">
                @if($lesson->video_url)
                    @if(str_starts_with($lesson->video_url, 'http') || str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                        @php
                            $embedUrl = $lesson->video_url;
                            if (str_contains($embedUrl, 'youtube.com/watch?v=')) {
                                $embedUrl = str_replace('watch?v=', 'embed/', $embedUrl);
                            } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                $embedUrl = str_replace('youtu.be/', 'youtube.com/embed/', $embedUrl);
                            }
                            // Ensure we have enablejsapi=1 for YouTube API to hook into it correctly
                            if (str_contains($embedUrl, '?')) {
                                $embedUrl .= '&enablejsapi=1';
                            } else {
                                $embedUrl .= '?enablejsapi=1';
                            }
                        @endphp
                        <iframe src="{{ $embedUrl }}" allowfullscreen></iframe>
                    @else
                        <video controls controlsList="nodownload" preload="metadata" style="width: 100%; height: 100%; object-fit: contain;">
                            <source src="{{ asset('storage/' . $lesson->video_url) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                @else
                    <div class="video-placeholder rounded-2xl">
                        <i class="fas fa-file-alt"></i>
                        <p>{{ __('This lesson has no video. Read the text content below.') }}</p>
                    </div>
                @endif
            </div>

            <!-- Детали урока (название, кнопки) -->
            <div class="lesson-info rounded-2xl border border-white/5 bg-slate-900/40 p-6 backdrop-blur-md">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <h2 class="text-2xl font-bold text-slate-100 m-0">{{ $lesson->title }}</h2>
                    @php
                        $isLessonCompleted = in_array($lesson->id, $completedLessonIds);
                    @endphp
                    <button id="btn-complete-lesson" 
                            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm {{ $isLessonCompleted ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-emerald-600/40 text-emerald-500 hover:bg-emerald-600/10' }}"
                            data-lesson-id="{{ $lesson->id }}"
                            data-complete-url="/student/lessons/{{ $lesson->id }}/complete"
                            data-uncomplete-url="/student/lessons/{{ $lesson->id }}/uncomplete">
                        <i class="fas {{ $isLessonCompleted ? 'fa-check-circle' : 'fa-circle' }}"></i>
                        <span class="btn-text">{{ $isLessonCompleted ? __('messages.progress.completed') : __('messages.progress.mark_completed') }}</span>
                    </button>
                </div>
                
                <div class="flex items-center justify-between flex-wrap gap-3 mt-4">
                    <div class="flex items-center gap-3 text-sm text-slate-400">
                        <span><i class="far fa-clock"></i> {{ $lesson->duration_minutes }} {{ __('minutes') }}</span>
                        @if($lesson->is_preview)
                            <span class="badge bg-success" style="padding: 4px 8px; border-radius: 6px;"><i class="fas fa-eye"></i> {{ __('Preview') }}</span>
                        @endif
                    </div>

                    @auth
                        @php
                            $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                                ->where('course_id', $course->id)
                                ->exists();
                        @endphp
                        <div class="flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm btn-toggle-list flex items-center gap-2"
                                    data-course-id="{{ $course->id }}"
                                    data-list-type="wishlist"
                                    data-toggle-url="{{ route('wishlist.toggle', $course->id) }}"
                                    style="border-radius: var(--radius-sm); font-size: 12.5px; padding: 6px 12px; color: var(--text-secondary); border-color: var(--border-light); background: rgba(255,255,255,0.02); transition: var(--transition);">
                                <i class="{{ $inWishlist ? 'fas fa-heart text-danger' : 'far fa-heart text-neutral-400' }}"></i>
                                <span class="btn-toggle-text">{{ __('messages.wishlist.title') }}</span>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm flex items-center gap-2"
                                    id="btn-share-course"
                                    data-url="{{ route('course.show', $course->slug) }}"
                                    style="border-radius: var(--radius-sm); font-size: 12.5px; padding: 6px 12px; color: var(--text-secondary); border-color: var(--border-light); background: rgba(255,255,255,0.02); transition: var(--transition);">
                                <i class="fas fa-share-nodes text-indigo-400"></i>
                                <span class="btn-share-text">{{ __('messages.progress.share') }}</span>
                            </button>
                        </div>
                    @endauth
                </div>

                <!-- NEW RATING BLOCK AND COMMENT SYSTEM INSIDE THE SAME CARD -->
                @auth
                    @php
                        $userReview = \App\Models\Review::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->where('lesson_id', $lesson->id)
                            ->first();
                        $currentRating = $userReview ? $userReview->rating : 0;
                    @endphp
                    <div class="lesson-rating-block mt-6 pt-4 border-t border-white/5 flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold text-neutral-300">{{ __('messages.progress.rate_lesson') }}:</span>
                            <div class="flex items-center gap-1 rating-stars" data-course-id="{{ $course->id }}" data-rating="{{ $currentRating }}" data-submit-url="{{ route('review.store', $course->id) }}">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button class="star-btn cursor-pointer transition-colors duration-200" data-value="{{ $i }}" style="background: none; border: none; padding: 0; outline: none;">
                                        <svg class="w-6 h-6 {{ $i <= $currentRating ? 'text-amber-400 fill-current' : 'text-neutral-500 fill-none' }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.15-.353.682-.353.832 0l2.366 5.56 6.111.472c.389.03.545.508.252.774l-4.59 4.19 1.428 5.952c.09.378-.313.67-.647.469L12 17.724l-5.432 3.199c-.334.201-.738-.09-.647-.469l1.428-5.952-4.59-4.19c-.293-.266-.137-.744.252-.774l6.111-.472 2.366-5.56z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                        </div>
                        <div id="rating-feedback" class="text-xs text-emerald-500 font-semibold" style="display: none; transition: opacity 0.3s ease;"></div>
                    </div>
                @endauth
            </div>

            <!-- СЕКЦИЯ С ВКЛАДКАМИ (КОНТЕНТ, КОММЕНТАРИИ, ИИ-МЕНТОР) -->
            <div class="rounded-2xl border border-white/5 bg-slate-900/40 p-6 backdrop-blur-md flex flex-col gap-6">
                <!-- Заголовки вкладок -->
                <div class="flex border-b border-white/10 pb-3 gap-6 overflow-x-auto scrollbar-none">
                    <button class="tab-trigger active text-base font-bold text-slate-100 hover:text-white pb-2 border-b-2 border-indigo-500 transition cursor-pointer whitespace-nowrap" data-target="tab-content">
                        {{ __('Lesson content') }}
                    </button>
                    <button class="tab-trigger text-base font-bold text-slate-400 hover:text-slate-200 pb-2 border-b-2 border-transparent transition cursor-pointer whitespace-nowrap" data-target="tab-comments">
                        {{ __('Comments') }} ({{ $lesson->comments->count() }})
                    </button>
                    <button class="tab-trigger text-base font-bold text-slate-400 hover:text-slate-200 pb-2 border-b-2 border-transparent transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap" data-target="tab-ai-mentor">
                        <i class="fa-solid fa-robot text-indigo-400 animate-pulse"></i>
                        <span>{{ __('messages.ai.mentor_tab') }}</span>
                    </button>
                </div>

                <!-- Содержимое вкладок -->
                <!-- 1. Описание урока -->
                <div id="tab-content" class="tab-pane">
                    <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{!! nl2br(e($lesson->content_text ?? $lesson->content)) !!}</div>
                </div>

                <!-- 2. Комментарии -->
                <div id="tab-comments" class="tab-pane hidden">
                    @auth
                        <form id="comment-form" class="flex flex-col gap-3 mb-6" data-submit-url="{{ route('comments.store') }}">
                            @csrf
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                            <div class="relative">
                                <textarea name="content" rows="3" required
                                          class="w-full rounded-xl border border-white/10 bg-slate-950/50 p-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                          placeholder="{{ __('Write the text of the comment.') }}"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-755 rounded-xl cursor-pointer shadow-md transition duration-200">
                                    {{ __('Send') }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 rounded-xl bg-slate-950/30 text-slate-400 text-sm mb-6 text-center">
                            <a href="{{ route('login') }}" class="text-indigo-400 hover:underline font-semibold">{{ __('Login') }}</a> {{ __('to leave a comment.') }}
                        </div>
                    @endauth

                    <!-- Список комментариев -->
                    <div id="comments-list" class="flex flex-col gap-4">
                        @forelse($lesson->comments as $comment)
                            <div class="p-4 rounded-xl bg-slate-950/30 border border-white/5 flex gap-3">
                                <!-- Аватар заглушка -->
                                <div class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                                    {{ substr($comment->user->name ?? 'U', 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <h4 class="text-sm font-semibold text-slate-200">{{ $comment->user->name ?? 'User' }}</h4>
                                        <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-300 break-words leading-relaxed">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <p id="no-comments-msg" class="text-sm text-slate-500 italic text-center py-4">{{ __('No comments yet. Be the first to comment!') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. ИИ-Ментор Чат -->
                <div id="tab-ai-mentor" class="tab-pane hidden">
                    <div class="flex flex-col h-[460px] bg-slate-950/40 border border-white/5 rounded-2xl overflow-hidden">
                        <!-- Шапка чата -->
                        <div class="flex items-center justify-between p-4 bg-slate-900/60 border-b border-white/5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid fa-robot text-xs text-indigo-400"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-200">{{ __('messages.ai.mentor_name') }}</h4>
                                    <span class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ __('messages.ai.status_online') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Список сообщений -->
                        <div id="ai-chat-messages" class="flex-1 p-4 overflow-y-auto space-y-4 scrollbar-thin">
                            <!-- Приветственное сообщение -->
                            <div class="flex gap-3 max-w-[85%]">
                                <div class="w-8 h-8 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid fa-robot text-xs"></i>
                                </div>
                                <div class="p-3 rounded-2xl rounded-tl-none bg-slate-900/60 border border-white/5 text-sm text-slate-300 leading-relaxed">
                                    {{ __('messages.ai.welcome_msg') }}
                                </div>
                            </div>
                        </div>

                        <!-- Панель ввода -->
                        <div class="p-3 bg-slate-900/40 border-t border-white/5">
                            @auth
                                <form id="ai-chat-form" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="text" id="ai-chat-input" required autocomplete="off"
                                           class="flex-grow rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                           placeholder="{{ __('messages.ai.ask_placeholder') }}">
                                    <button type="submit" class="p-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl cursor-pointer shadow-md transition duration-200 flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('messages.ai.send_btn') }}">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                    </button>
                                </form>
                            @else
                                <div class="p-3 rounded-xl bg-slate-950/30 text-slate-400 text-sm text-center">
                                    <a href="{{ route('login') }}" class="text-indigo-400 hover:underline font-semibold">{{ __('Login') }}</a> {{ __('to ask questions.') }}
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка (1/3 ширины) - Course Program -->
        <div class="lg:col-span-1">
            <div class="lesson-sidebar rounded-2xl border border-white/5 bg-slate-900/40 overflow-hidden backdrop-blur-md sticky top-24">
                <div class="sidebar-header p-5 border-b border-white/5">
                    <h3 class="text-base font-bold text-slate-100">{{ __('Course program') }}</h3>
                    <div class="sidebar-progress mt-3">
                        <div class="progress-wrap w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <div class="progress-bar h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $progress }}%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-400 mt-2">
                            <span>{{ $completedCount }}/{{ $totalLessons }}</span>
                            <span>{{ $progress }}%</span>
                        </div>
                    </div>
                </div>

                <div class="lesson-list divide-y divide-white/5 max-h-[50vh] overflow-y-auto">
                    @foreach($course->lessons as $l)
                        @php
                            $isActive = $l->id === $lesson->id;
                            $isCompleted = in_array($l->id, $completedLessonIds);
                        @endphp
                        <a href="{{ route('course.learn', ['slug' => $course->slug, 'lesson' => $l->id]) }}"
                           class="lesson-list-item flex items-center gap-3 p-4 hover:bg-white/5 transition duration-200 no-underline {{ $isActive ? 'bg-indigo-600/10 border-l-4 border-indigo-500' : '' }} {{ $isCompleted ? 'completed' : '' }}"
                           data-lesson-id="{{ $l->id }}"
                           data-iteration="{{ $loop->iteration }}">
                            <div class="lesson-num w-8 h-8 rounded-full bg-slate-950/50 flex items-center justify-center text-xs font-bold text-slate-400 shrink-0">
                                @if($isCompleted)
                                    <svg class="w-5 h-5 text-green-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            <div class="lesson-item-body flex-1 min-w-0">
                                <div class="text-sm font-semibold text-slate-200 truncate">{{ $l->title }}</div>
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-1">
                                    @if($l->video_url)
                                        <i class="fas fa-play-circle text-[10px]"></i> {{ __('Video') }}
                                    @else
                                        <i class="fas fa-file-alt text-[10px]"></i> {{ __('Text') }}
                                    @endif
                                </div>
                            </div>
                            @if($isActive)
                                <i class="fas fa-volume-up text-indigo-400 shrink-0"></i>
                            @elseif($l->video_url)
                                <i class="fas fa-play-circle text-slate-500 shrink-0"></i>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="lesson-nav p-4 bg-slate-950/20 border-t border-white/5 flex items-center justify-between gap-3">
                    @php
                        $lessons = $course->lessons;
                        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
                        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
                        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;
                    @endphp

                    @if($prevLesson)
                        <a href="{{ route('course.learn', ['slug' => $course->slug, 'lesson' => $prevLesson->id]) }}"
                           class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-700 text-slate-300 hover:bg-white/5 hover:text-white transition duration-200 no-underline">
                            <i class="fas fa-chevron-left"></i> {{ __('Back') }}
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('course.learn', ['slug' => $course->slug, 'lesson' => $nextLesson->id]) }}"
                           class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition duration-200 no-underline">
                            {{ __('Next') }} <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <a href="{{ route('course.show', $course->slug) }}" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition duration-200 no-underline">
                            <i class="fas fa-flag-checkered"></i> {{ __('Complete the course') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnComplete = document.getElementById('btn-complete-lesson');
        if (btnComplete) {
            btnComplete.addEventListener('click', function() {
                const lessonId = this.getAttribute('data-lesson-id');
                const isCompleted = this.classList.contains('bg-emerald-600');
                const url = isCompleted ? this.getAttribute('data-uncomplete-url') : this.getAttribute('data-complete-url');

                toggleLessonProgress(lessonId, !isCompleted, url);
            });
        }

        // Native HTML5 video ended detection
        const videoElement = document.querySelector('.video-player video');
        if (videoElement) {
            videoElement.addEventListener('ended', function() {
                if (btnComplete && !btnComplete.classList.contains('bg-emerald-600')) {
                    const lessonId = btnComplete.getAttribute('data-lesson-id');
                    const url = btnComplete.getAttribute('data-complete-url');
                    toggleLessonProgress(lessonId, true, url);
                }
            });
        }

        // YouTube API ended detection
        const iframe = document.querySelector('.video-player iframe');
        if (iframe && (iframe.src.includes('youtube.com') || iframe.src.includes('youtu.be'))) {
            // Load YouTube API asynchronously
            const tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            const firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        }
    });

    // YouTube Iframe API callback
    let ytPlayer;
    window.onYouTubeIframeAPIReady = function() {
        const iframe = document.querySelector('.video-player iframe');
        if (iframe) {
            let src = iframe.src;
            if (!src.includes('enablejsapi=1')) {
                src += (src.includes('?') ? '&' : '?') + 'enablejsapi=1';
                iframe.src = src;
            }
            
            ytPlayer = new YT.Player(iframe, {
                events: {
                    'onStateChange': function(event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            const btnComplete = document.getElementById('btn-complete-lesson');
                            if (btnComplete && !btnComplete.classList.contains('bg-emerald-600')) {
                                const lessonId = btnComplete.getAttribute('data-lesson-id');
                                const url = btnComplete.getAttribute('data-complete-url');
                                toggleLessonProgress(lessonId, true, url);
                            }
                        }
                    }
                }
            });
        }
    };

    function toggleLessonProgress(lessonId, markAsComplete, url) {
        const btnComplete = document.getElementById('btn-complete-lesson');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ lesson_id: lessonId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // 1. Update button classes & texts
                if (markAsComplete) {
                    btnComplete.className = 'flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm bg-emerald-600 text-white hover:bg-emerald-700';
                    btnComplete.querySelector('i').className = 'fas fa-check-circle';
                    btnComplete.querySelector('.btn-text').textContent = "{{ __('messages.progress.completed') }}";
                } else {
                    btnComplete.className = 'flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm border border-emerald-600/40 text-emerald-500 hover:bg-emerald-600/10';
                    btnComplete.querySelector('i').className = 'fas fa-circle';
                    btnComplete.querySelector('.btn-text').textContent = "{{ __('messages.progress.mark_completed') }}";
                }

                // 2. Update sidebar counter & progress bar
                const progressText = document.querySelector('.sidebar-progress span');
                if (progressText) {
                    progressText.textContent = `${data.completedCount}/${data.totalLessons} · ${data.progress}%`;
                }
                const progressBar = document.querySelector('.sidebar-progress .progress-bar');
                if (progressBar) {
                    progressBar.style.width = `${data.progress}%`;
                }

                // 3. Update sidebar checkmark icon for this lesson
                const lessonItem = document.querySelector(`.lesson-list-item[data-lesson-id="${lessonId}"]`);
                if (lessonItem) {
                    const numDiv = lessonItem.querySelector('.lesson-num');
                    if (markAsComplete) {
                        lessonItem.classList.add('completed');
                        numDiv.innerHTML = `<svg class="w-5 h-5 text-green-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>`;
                    } else {
                        lessonItem.classList.remove('completed');
                        // Reset back to correct lesson number
                        numDiv.innerHTML = lessonItem.getAttribute('data-iteration');
                    }
                }

                // 4. Alert if certificate earned
                if (data.certificateEarned) {
                    alert("Congratulations! You have completed all lessons and earned a certificate!");
                }
            }
        })
        .catch(error => {
            console.error('Error updating progress:', error);
        });
    }

    // Add to Playlist, Wishlist and Watch Later
    const toggleListButtons = document.querySelectorAll('.btn-toggle-list');
    toggleListButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-toggle-url');
            const listType = this.getAttribute('data-list-type');
            const btnObj = this;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.success) {
                    const icon = btnObj.querySelector('i');
                    const textSpan = btnObj.querySelector('.btn-toggle-text');
                    const isAdded = data.added !== undefined ? data.added : (data.action === 'added');

                    if (listType === 'wishlist') {
                        if (isAdded) {
                            icon.className = 'fas fa-heart text-danger';
                        } else {
                            icon.className = 'far fa-heart text-neutral-400';
                        }
                    } else if (listType === 'playlist') {
                        if (isAdded) {
                            icon.className = 'fas fa-heart text-indigo-500';
                            textSpan.textContent = "{{ __('messages.progress.in_playlist') }}";
                        } else {
                            icon.className = 'far fa-heart text-neutral-400';
                            textSpan.textContent = "{{ __('messages.progress.add_playlist') }}";
                        }
                    } else if (listType === 'watch_later') {
                        if (isAdded) {
                            icon.className = 'fas fa-clock text-indigo-500';
                            textSpan.textContent = "{{ __('messages.progress.in_watch_later') }}";
                        } else {
                            icon.className = 'far fa-clock text-neutral-400';
                            textSpan.textContent = "{{ __('messages.progress.add_watch_later') }}";
                        }
                    }
                }
            })
            .catch(err => console.error('Error toggling list:', err));
        });
    });

    // Share button copy link functionality
    const shareBtn = document.getElementById('btn-share-course');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(() => {
                const textSpan = this.querySelector('.btn-share-text');
                const originalText = textSpan.textContent;
                textSpan.textContent = "{{ __('messages.progress.link_copied') }}";
                this.style.borderColor = '#10b981';
                setTimeout(() => {
                    textSpan.textContent = originalText;
                    this.style.borderColor = '';
                }, 2000);
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        });
    }

    // Star rating hover and click behavior
    const ratingContainer = document.querySelector('.rating-stars');
    if (ratingContainer) {
        const stars = ratingContainer.querySelectorAll('.star-btn');
        const feedback = document.getElementById('rating-feedback');
        let currentRating = parseInt(ratingContainer.getAttribute('data-rating')) || 0;
        const submitUrl = ratingContainer.getAttribute('data-submit-url');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateStarsDisplay(rating) {
            stars.forEach((star, index) => {
                const svg = star.querySelector('svg');
                if (index < rating) {
                    svg.classList.remove('text-neutral-500', 'fill-none');
                    svg.classList.add('text-amber-400', 'fill-current');
                } else {
                    svg.classList.remove('text-amber-400', 'fill-current');
                    svg.classList.add('text-neutral-500', 'fill-none');
                }
            });
        }

        stars.forEach(star => {
            // Hover logic
            star.addEventListener('mouseenter', function() {
                const hoverVal = parseInt(this.getAttribute('data-value'));
                updateStarsDisplay(hoverVal);
            });

            // Click logic
            star.addEventListener('click', function() {
                const selectedVal = parseInt(this.getAttribute('data-value'));
                currentRating = selectedVal;
                ratingContainer.setAttribute('data-rating', currentRating);
                updateStarsDisplay(currentRating);

                // Send via AJAX
                fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ rating: selectedVal, lesson_id: "{{ $lesson->id }}" })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (feedback) {
                            feedback.textContent = "{{ __('messages.progress.thanks_rating') }}";
                            feedback.style.display = 'block';
                            feedback.style.opacity = '1';
                            setTimeout(() => {
                                feedback.style.opacity = '0';
                                setTimeout(() => {
                                    feedback.style.display = 'none';
                                }, 300);
                            }, 3000);
                        }
                    }
                })
                .catch(err => console.error('Error submitting rating:', err));
            });
        });

        // Mouse leave container -> reset to currentRating
        ratingContainer.addEventListener('mouseleave', function() {
            updateStarsDisplay(currentRating);
        });
    }

    // Comment form submission via AJAX
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitUrl = this.getAttribute('data-submit-url');
            const token = this.querySelector('input[name="_token"]').value;
            const textarea = this.querySelector('textarea[name="content"]');
            const lessonId = this.querySelector('input[name="lesson_id"]').value;
            const content = textarea.value;

            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ lesson_id: lessonId, content: content })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    textarea.value = '';
                    const list = document.getElementById('comments-list');
                    const noCommentsMsg = document.getElementById('no-comments-msg');
                    if (noCommentsMsg) noCommentsMsg.remove();

                    const commentEl = document.createElement('div');
                    commentEl.className = 'p-4 rounded-xl bg-slate-950/30 border border-white/5 flex gap-3';
                    const initials = (data.comment.user.name || 'User').substring(0, 2).toUpperCase();

                    commentEl.innerHTML = `
                        <div class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                            \${initials}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h4 class="text-sm font-semibold text-slate-200">\${data.comment.user.name || 'User'}</h4>
                                <span class="text-xs text-slate-500">${__('just now')}</span>
                            </div>
                            <p class="text-sm text-slate-300 break-words leading-relaxed">\${data.comment.content}</p>
                        </div>
                    `;
                    list.insertBefore(commentEl, list.firstChild);

                    const title = document.querySelector('.comments-section h3');
                    if (title) {
                        const countMatch = title.textContent.match(/\\d+/);
                        if (countMatch) {
                            const newCount = parseInt(countMatch[0]) + 1;
                            title.textContent = title.textContent.replace(/\\d+/, newCount);
                        }
                    }
                }
            })
            .catch(err => console.error('Error posting comment:', err));
        });
    }

    // --- ЛОГИКА ПЕРЕКЛЮЧЕНИЯ ВКЛАДОК ---
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');

            // Убираем активные классы со всех вкладок
            tabTriggers.forEach(t => {
                t.classList.remove('active', 'text-slate-100', 'border-indigo-500');
                t.classList.add('text-slate-400', 'border-transparent');
            });

            // Добавляем активные классы текущей вкладке
            this.classList.add('active', 'text-slate-100', 'border-indigo-500');
            this.classList.remove('text-slate-400', 'border-transparent');

            // Скрываем все панели
            tabPanes.forEach(pane => pane.classList.add('hidden'));

            // Показываем нужную панель
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.remove('hidden');
                if (targetId === 'tab-ai-mentor') {
                    scrollToBottom();
                }
            }
        });
    });

    // --- ЛОГИКА ЧАТА С ИИ ---
    const aiForm = document.getElementById('ai-chat-form');
    const aiInput = document.getElementById('ai-chat-input');
    const aiMessages = document.getElementById('ai-chat-messages');

    if (aiForm) {
        aiForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageText = aiInput.value.trim();
            if (!messageText) return;

            // Очищаем инпут
            aiInput.value = '';

            // Добавляем сообщение пользователя в чат
            const userMsgHtml = `
                <div class="flex gap-3 max-w-[85%] ml-auto justify-end">
                    <div class="p-3 rounded-2xl rounded-tr-none bg-indigo-600/80 text-sm text-white leading-relaxed">
                        ${escapeHTML(messageText)}
                    </div>
                </div>
            `;
            aiMessages.insertAdjacentHTML('beforeend', userMsgHtml);
            scrollToBottom();

            // Создаем уникальный ID для лоадера
            const typingIndicatorId = 'ai-typing-' + Date.now();
            const typingHtml = `
                <div class="flex gap-3 max-w-[85%]" id="${typingIndicatorId}">
                    <div class="w-8 h-8 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-robot text-xs"></i>
                    </div>
                    <div class="p-3 rounded-2xl rounded-tl-none bg-slate-900/60 border border-white/5 text-sm text-slate-400 leading-relaxed flex items-center gap-1.5">
                        <span class="text-xs italic">${"{{ __('messages.ai.thinking') }}"}</span>
                        <span class="flex gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 0ms"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 150ms"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 300ms"></span>
                        </span>
                    </div>
                </div>
            `;
            aiMessages.insertAdjacentHTML('beforeend', typingHtml);
            scrollToBottom();

            // Отправляем AJAX запрос
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch("{{ route('student.ai-chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: messageText })
            })
            .then(res => res.json())
            .then(data => {
                // Удаляем индикатор загрузки
                const indicator = document.getElementById(typingIndicatorId);
                if (indicator) indicator.remove();

                if (data.success) {
                    // Рендерим Markdown с помощью Marked или через фоллбек
                    let parsedReply = data.reply;
                    if (typeof marked !== 'undefined' && typeof marked.parse === 'function') {
                        parsedReply = marked.parse(data.reply);
                    } else {
                        parsedReply = escapeHTML(data.reply)
                            .replace(/\n/g, '<br>')
                            .replace(/```([\s\S]*?)```/g, '<pre class="bg-slate-950 p-3 rounded-xl border border-white/5 my-2 text-xs font-mono overflow-x-auto"><code>$1</code></pre>');
                    }

                    const aiMsgHtml = `
                        <div class="flex gap-3 max-w-[85%]">
                            <div class="w-8 h-8 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0">
                                <i class="fa-solid fa-robot text-xs"></i>
                            </div>
                            <div class="p-3 rounded-2xl rounded-tl-none bg-slate-900/60 border border-white/5 text-sm text-slate-300 leading-relaxed ai-markdown-content">
                                ${parsedReply}
                            </div>
                        </div>
                    `;
                    aiMessages.insertAdjacentHTML('beforeend', aiMsgHtml);
                } else {
                    showErrorMsg();
                }
                scrollToBottom();
            })
            .catch(err => {
                console.error('AI chat error:', err);
                const indicator = document.getElementById(typingIndicatorId);
                if (indicator) indicator.remove();
                showErrorMsg();
                scrollToBottom();
            });
        });
    }

    function showErrorMsg() {
        const errorMsgHtml = `
            <div class="flex gap-3 max-w-[85%]">
                <div class="w-8 h-8 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center font-bold text-sm shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                </div>
                <div class="p-3 rounded-2xl rounded-tl-none bg-red-955/20 border border-red-500/20 text-sm text-red-400 leading-relaxed">
                    ${"{{ __('messages.ai.error') }}"}
                </div>
            </div>
        `;
        aiMessages.insertAdjacentHTML('beforeend', errorMsgHtml);
    }

    function scrollToBottom() {
        if (aiMessages) {
            aiMessages.scrollTop = aiMessages.scrollHeight;
        }
    }

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }
});
</script>
@endsection