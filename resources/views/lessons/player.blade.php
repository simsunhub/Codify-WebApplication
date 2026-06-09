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
    .toggle-chevron {
        transition: transform 0.2s ease;
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
                    <button id="mark-completed-btn" 
                            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm {{ $isLessonCompleted ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-emerald-600/40 text-emerald-500 hover:bg-emerald-600/10' }}"
                            data-lesson-id="{{ $lesson->id }}">
                        <i class="fas {{ $isLessonCompleted ? 'fa-check-circle' : 'fa-circle' }}"></i>
                        <span class="btn-text">{!! $isLessonCompleted ? '<span class="text-green-400">✓ ' . __('messages.progress.completed') . '</span>' : __('messages.progress.mark_completed') !!}</span>
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
                            <div class="flex items-center gap-1 rating-stars" data-course-id="{{ $course->id }}" data-rating="{{ $currentRating }}">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button class="star-btn rating-star cursor-pointer transition-colors duration-200 {{ $i <= $currentRating ? 'text-amber-400' : 'text-slate-500' }}" data-value="{{ $i }}" style="background: none; border: none; padding: 0; outline: none;">
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
                    <button class="tab-trigger text-base font-bold text-slate-400 hover:text-slate-200 pb-2 border-b-2 border-transparent transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap" data-target="tab-discussions">
                        <i class="fa-solid fa-comments text-indigo-400"></i>
                        <span>{{ __('messages.dash.discussions_qa') }}</span>
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
                        <form id="comment-form" class="flex flex-col gap-3 mb-6">
                            @csrf
                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                            <div class="relative">
                                <textarea name="content" rows="3" required
                                          class="w-full rounded-xl border border-white/10 bg-slate-950/50 p-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                          placeholder="{{ __('Write the text of the comment.') }}"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-750 rounded-xl cursor-pointer shadow-md transition duration-200">
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

                <!-- 4. Вопросы и ответы (Q&A) -->
                <div id="tab-discussions" class="tab-pane hidden flex flex-col gap-6">
                    <!-- Ask Question Form -->
                    @auth
                        <div class="p-5 rounded-2xl border border-white/5 bg-slate-955/40 flex flex-col gap-4">
                            <h4 class="text-sm font-semibold text-slate-200">{{ __('messages.progress.ask_question') }}</h4>
                            <form id="discussion-form" class="flex flex-col gap-3">
                                @csrf
                                <div>
                                    <input type="text" name="title" required placeholder="{{ __('messages.progress.question_topic') }}"
                                           class="w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                </div>
                                <div>
                                    <textarea name="body" rows="4" required placeholder="{{ __('messages.progress.question_details') }}"
                                              class="w-full rounded-xl border border-white/10 bg-slate-950/50 p-4 text-sm text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-750 rounded-xl cursor-pointer shadow-md transition duration-200">
                                        {{ __('messages.progress.publish_question') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-950/30 text-slate-400 text-sm text-center">
                            <a href="{{ route('login') }}" class="text-indigo-400 hover:underline font-semibold">{{ __('Login') }}</a> {{ __('to ask questions.') }}
                        </div>
                    @endauth

                    <!-- Discussions List -->
                    <div id="discussions-list" class="flex flex-col gap-4">
                        @forelse($discussions as $disc)
                            <div class="discussion-item p-4 rounded-xl bg-slate-950/30 border border-white/5 flex flex-col gap-3 transition duration-200" data-discussion-id="{{ $disc->id }}">
                                <!-- Discussion Header -->
                                <div class="flex items-start justify-between gap-4 cursor-pointer toggle-discussion-btn">
                                    <div class="flex gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                                            {{ substr($disc->user->name ?? 'U', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-sm font-semibold text-slate-200">{{ $disc->user->name ?? 'User' }}</h4>
                                                <span class="text-xs text-slate-500">{{ $disc->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h3 class="text-base font-bold text-slate-100 mt-1 discussion-title-text">{{ $disc->title }}</h3>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="badge-answered-status">
                                            @if($disc->is_answered)
                                                <span class="px-2 py-0.5 text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                                                    <i class="fa-solid fa-check me-1"></i>{{ __('messages.progress.resolved') }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-[11px] font-semibold text-amber-400 bg-amber-500/10 rounded-full border border-amber-500/20">
                                                    <i class="fa-solid fa-clock me-1"></i>{{ __('messages.progress.waiting_response') }}
                                                </span>
                                            @endif
                                        </span>
                                        <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200 toggle-chevron"></i>
                                    </div>
                                </div>

                                <!-- Collapsible Content & Replies -->
                                <div class="discussion-details hidden border-t border-white/5 pt-3 mt-1 flex flex-col gap-4">
                                    <!-- Discussion Body -->
                                    <div class="text-sm text-slate-300 whitespace-pre-wrap bg-slate-950/20 p-3 rounded-lg border border-white/5">{{ $disc->body }}</div>

                                    <!-- Replies Container -->
                                    <div class="replies-container flex flex-col gap-3 pl-2 sm:pl-4 border-l border-white/10">
                                        <h5 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">{{ __('messages.progress.replies') }} (<span class="replies-count-num">{{ $disc->replies->count() }}</span>)</h5>
                                        <div class="replies-list flex flex-col gap-3">
                                            @forelse($disc->replies as $reply)
                                                @php
                                                    $isReplierTeacher = ($reply->user_id === $course->instructor_id || $reply->user_id === $course->user_id);
                                                @endphp
                                                <div class="p-3 rounded-lg bg-slate-950/20 border border-white/5 flex gap-2">
                                                    <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-xs shrink-0 uppercase">
                                                        {{ substr($reply->user->name ?? 'U', 0, 2) }}
                                                    </div>
                                                    <div class="flex-grow min-w-0">
                                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                                            <span class="text-xs font-semibold text-slate-200">{{ $reply->user->name ?? 'User' }}</span>
                                                            @if($isReplierTeacher)
                                                                <span class="px-1.5 py-0.2 text-[9px] font-bold text-indigo-400 bg-indigo-500/10 rounded border border-indigo-500/20 uppercase">
                                                                    {{ __('messages.progress.instructor_label') }}
                                                                </span>
                                                            @endif
                                                            <span class="text-[10px] text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-sm text-slate-300 break-words">{{ $reply->body }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="no-replies-msg text-xs text-slate-500 italic">{{ __('messages.progress.no_replies') }}</p>
                                            @endforelse
                                        </div>

                                        <!-- Reply Form -->
                                        @auth
                                            <form class="reply-form flex gap-2 mt-2" data-discussion-id="{{ $disc->id }}">
                                                @csrf
                                                <input type="text" name="body" required placeholder="{{ __('messages.progress.write_reply') }}"
                                                       class="flex-grow rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-550 rounded-xl cursor-pointer shadow-md transition duration-200 shrink-0">
                                                    {{ __('messages.progress.reply_btn') }}
                                                </button>
                                            </form>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p id="no-discussions-msg" class="text-sm text-slate-500 italic text-center py-6">{{ __('messages.progress.no_questions') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка (1/3 ширины) - Course Program -->
        <div class="lg:col-span-1">
            <div class="lesson-sidebar rounded-2xl border border-white/5 bg-slate-900/40 overflow-hidden backdrop-blur-md lg:sticky lg:top-24">
                <div class="sidebar-header p-5 border-b border-white/5">
                    <h3 class="text-base font-bold text-slate-100">{{ __('Course program') }}</h3>
                    <div class="sidebar-progress mt-3">
                        <div class="progress-wrap w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <div class="progress-bar progress-bar-selector h-full bg-emerald-500 rounded-full transition-all duration-300" style="width: {{ $progress }}%;"></div>
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
                            <div id="playlist-icon-{{ $l->id }}" class="lesson-num w-8 h-8 rounded-full bg-slate-950/50 flex items-center justify-center text-xs font-bold text-slate-400 shrink-0">
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
    // CSRF Token helper
    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    // 1. Кнопка "Отметить как пройденный"
    const completeBtn = document.getElementById('mark-completed-btn');
    if (completeBtn) {
        completeBtn.addEventListener('click', function() {
            const lessonId = this.getAttribute('data-lesson-id');
            toggleLessonProgress(lessonId);
        });
    }

    function toggleLessonProgress(lessonId) {
        fetch(`/student/lessons/${lessonId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update completeBtn styling & text
                    if (data.completed) {
                        completeBtn.className = 'flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm bg-emerald-600 text-white hover:bg-emerald-700';
                        completeBtn.querySelector('i').className = 'fas fa-check-circle';
                        completeBtn.querySelector('.btn-text').innerHTML = '<span class="text-green-400">✓ ' + "{{ __('messages.progress.completed') }}" + '</span>';
                    } else {
                        completeBtn.className = 'flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 cursor-pointer shadow-sm border border-emerald-600/40 text-emerald-500 hover:bg-emerald-600/10';
                        completeBtn.querySelector('i').className = 'fas fa-circle';
                        completeBtn.querySelector('.btn-text').textContent = "{{ __('messages.progress.mark_completed') }}";
                    }
                
                // Update playlist item checkmark/number
                const playlistIcon = document.getElementById(`playlist-icon-${lessonId}`);
                if (playlistIcon) {
                    const listItem = playlistIcon.closest('.lesson-list-item');
                    if (data.completed) {
                        if (listItem) listItem.classList.add('completed');
                        playlistIcon.innerHTML = '<svg class="w-5 h-5 text-green-500 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                    } else {
                        if (listItem) {
                            listItem.classList.remove('completed');
                            const iteration = listItem.getAttribute('data-iteration') || '1';
                            playlistIcon.innerHTML = iteration;
                        }
                    }
                }
                
                // Update progress bar
                const progressBar = document.querySelector('.progress-bar-selector');
                if (progressBar) {
                    progressBar.style.width = data.progress + '%';
                }
                
                // Update progress counter text
                const progressCounter = document.querySelector('.sidebar-progress .flex span:first-child');
                if (progressCounter && data.completedCount !== undefined && data.totalLessons !== undefined) {
                    progressCounter.textContent = `${data.completedCount}/${data.totalLessons}`;
                }
                const progressPercent = document.querySelector('.sidebar-progress .flex span:last-child');
                if (progressPercent && data.progress !== undefined) {
                    progressPercent.textContent = `${data.progress}%`;
                }

                if (data.certificateEarned) {
                    alert("{{ __('messages.progress.cert_earned') }}");
                }
            }
        })
        .catch(err => console.error('Error completing lesson:', err));
    }

    // Expose for external access like YouTube API callback
    window.toggleLessonProgress = toggleLessonProgress;

    // 2. Кнопка "В избранное" (Wishlist)
    document.querySelectorAll('.btn-toggle-list').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-toggle-url');
            const listType = this.getAttribute('data-list-type');
            const btnObj = this;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
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
                    }
                }
            })
            .catch(err => console.error('Error toggling wishlist:', err));
        });
    });

    // 2.1 Кнопка "Поделиться"
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

    // 3. Интерактивные Звездочки Рейтинга
    const stars = document.querySelectorAll('.rating-star');
    const ratingContainer = document.querySelector('.rating-stars');
    const feedback = document.getElementById('rating-feedback');
    let currentRating = ratingContainer ? parseInt(ratingContainer.getAttribute('data-rating')) || 0 : 0;

    function updateStarsDisplay(rating) {
        stars.forEach(s => {
            const val = parseInt(s.getAttribute('data-value'));
            const svg = s.querySelector('svg');
            if (val <= rating) {
                s.classList.add('text-amber-400');
                s.classList.remove('text-slate-500');
                if (svg) {
                    svg.className.baseVal = "w-6 h-6 text-amber-400 fill-current";
                }
            } else {
                s.classList.remove('text-amber-400');
                s.classList.add('text-slate-500');
                if (svg) {
                    svg.className.baseVal = "w-6 h-6 text-neutral-500 fill-none";
                }
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const hoverVal = parseInt(this.getAttribute('data-value'));
            updateStarsDisplay(hoverVal);
        });

        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-value');
            const lessonId = completeBtn ? completeBtn.getAttribute('data-lesson-id') : null;
            if (!lessonId) return;

            fetch(`/student/lessons/${lessonId}/review`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ rating: rating })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentRating = parseInt(rating);
                    if (ratingContainer) {
                        ratingContainer.setAttribute('data-rating', currentRating);
                    }
                    updateStarsDisplay(currentRating);

                    if (feedback) {
                        feedback.textContent = "Спасибо за оценку!";
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
            .catch(err => console.error('Error storing review:', err));
        });
    });

    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', function() {
            updateStarsDisplay(currentRating);
        });
    }

    // 4. Форма комментариев (Submit via POST и добавление в конец списка)
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const textarea = this.querySelector('textarea[name="content"]');
            const content = textarea.value;
            const lessonId = this.querySelector('input[name="lesson_id"]').value;
            if (!content.trim() || !lessonId) return;

            fetch(`/student/lessons/${lessonId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ content: content })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    textarea.value = '';
                    
                    const commentsList = document.getElementById('comments-list');
                    const noCommentsMsg = document.getElementById('no-comments-msg');
                    if (noCommentsMsg) {
                        noCommentsMsg.remove();
                    }

                    const commentEl = document.createElement('div');
                    commentEl.className = 'p-4 rounded-xl bg-slate-950/30 border border-white/5 flex gap-3';
                    const userName = data.comment.user ? data.comment.user.name : 'User';
                    const initials = userName.substring(0, 2).toUpperCase();

                    commentEl.innerHTML = `
                        <div class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                            ${initials}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h4 class="text-sm font-semibold text-slate-200">${userName}</h4>
                                <span class="text-xs text-slate-500">Только что</span>
                            </div>
                            <p class="text-sm text-slate-300 break-words leading-relaxed">${data.comment.content}</p>
                        </div>
                    `;
                    // Append to the bottom
                    commentsList.appendChild(commentEl);

                    // Update count
                    const header = document.querySelector('.comments-section h3');
                    if (header) {
                        const countMatch = header.textContent.match(/\d+/);
                        if (countMatch) {
                            const newCount = parseInt(countMatch[0]) + 1;
                            header.textContent = header.textContent.replace(/\d+/, newCount);
                        }
                    }
                }
            })
            .catch(err => console.error('Error posting comment:', err));
        });
    }

    // 5. Видео ended авто-завершение
    const videoElement = document.querySelector('.video-player video');
    if (videoElement) {
        videoElement.addEventListener('ended', function() {
            if (completeBtn && !completeBtn.classList.contains('bg-emerald-600')) {
                const lessonId = completeBtn.getAttribute('data-lesson-id');
                toggleLessonProgress(lessonId);
            }
        });
    }

    // YouTube API ended авто-завершение
    const iframe = document.querySelector('.video-player iframe');
    if (iframe && (iframe.src.includes('youtube.com') || iframe.src.includes('youtu.be'))) {
        const tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
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
            fetch("{{ route('student.ai-chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
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

    // --- ЛОГИКА ОБСУЖДЕНИЙ / ВОПРОСОВ И ОТВЕТОВ (Q&A) ---
    // 1. Аккордеон для раскрытия деталей обсуждения и ответов
    document.addEventListener('click', function(e) {
        const header = e.target.closest('.toggle-discussion-btn');
        if (header) {
            const item = header.closest('.discussion-item');
            const details = item.querySelector('.discussion-details');
            const chevron = item.querySelector('.toggle-chevron');
            if (details) {
                details.classList.toggle('hidden');
                if (chevron) {
                    chevron.style.transform = details.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            }
        }
    });

    // 2. Отправка нового вопроса по AJAX
    const discussionForm = document.getElementById('discussion-form');
    if (discussionForm) {
        discussionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const titleInput = this.querySelector('input[name="title"]');
            const bodyTextarea = this.querySelector('textarea[name="body"]');
            const title = titleInput.value.trim();
            const body = bodyTextarea.value.trim();
            if (!title || !body) return;

            fetch("{{ route('student.discussions.store', $course->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ title: title, body: body })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    titleInput.value = '';
                    bodyTextarea.value = '';

                    const list = document.getElementById('discussions-list');
                    const noMsg = document.getElementById('no-discussions-msg');
                    if (noMsg) noMsg.remove();

                    const disc = data.discussion;
                    const initials = (disc.user ? disc.user.name : 'User').substring(0, 2).toUpperCase();
                    const userName = disc.user ? disc.user.name : 'User';

                    const html = `
                        <div class="discussion-item p-4 rounded-xl bg-slate-950/30 border border-white/5 flex flex-col gap-3 transition duration-200" data-discussion-id="${disc.id}">
                            <div class="flex items-start justify-between gap-4 cursor-pointer toggle-discussion-btn">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm shrink-0 uppercase">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-semibold text-slate-200">${userName}</h4>
                                            <span class="text-xs text-slate-500">только что</span>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-100 mt-1 discussion-title-text">${escapeHTML(disc.title)}</h3>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="badge-answered-status">
                                        <span class="px-2 py-0.5 text-[11px] font-semibold text-amber-400 bg-amber-500/10 rounded-full border border-amber-500/20">
                                            <i class="fa-solid fa-clock me-1"></i>ожидает ответа
                                        </span>
                                    </span>
                                    <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200 toggle-chevron"></i>
                                </div>
                            </div>
                            <div class="discussion-details hidden border-t border-white/5 pt-3 mt-1 flex flex-col gap-4">
                                <div class="text-sm text-slate-300 whitespace-pre-wrap bg-slate-950/20 p-3 rounded-lg border border-white/5">${escapeHTML(disc.body)}</div>
                                <div class="replies-container flex flex-col gap-3 pl-4 border-l border-white/10">
                                    <h5 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Ответы (<span class="replies-count-num">0</span>)</h5>
                                    <div class="replies-list flex flex-col gap-3">
                                        <p class="no-replies-msg text-xs text-slate-500 italic">Ответов пока нет.</p>
                                    </div>
                                    <form class="reply-form flex gap-2 mt-2" data-discussion-id="${disc.id}">
                                        <input type="text" name="body" required placeholder="Напишите ваш ответ..."
                                               class="flex-grow rounded-xl border border-white/10 bg-slate-950/50 px-4 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl cursor-pointer shadow-md transition duration-200 shrink-0">
                                            Ответить
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('afterbegin', html);
                }
            })
            .catch(err => console.error('Error creating discussion:', err));
        });
    }

    // 3. Отправка ответа на существующий вопрос по AJAX
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.reply-form');
        if (form) {
            e.preventDefault();
            const discussionId = form.getAttribute('data-discussion-id');
            const bodyInput = form.querySelector('input[name="body"]');
            const body = bodyInput.value.trim();
            if (!body || !discussionId) return;

            fetch(`/student/discussions/${discussionId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ body: body })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    bodyInput.value = '';

                    const item = form.closest('.discussion-item');
                    const repliesList = item.querySelector('.replies-list');
                    const noReplies = item.querySelector('.no-replies-msg');
                    if (noReplies) noReplies.remove();

                    const reply = data.reply;
                    const initials = (reply.user ? reply.user.name : 'User').substring(0, 2).toUpperCase();
                    const userName = reply.user ? reply.user.name : 'User';

                    const instructorId = parseInt("{{ $course->instructor_id ?? $course->user_id ?? 0 }}");
                    const isReplierTeacher = reply.user_id === instructorId;
                    const teacherTag = isReplierTeacher ? `<span class="px-1.5 py-0.2 text-[9px] font-bold text-indigo-400 bg-indigo-500/10 rounded border border-indigo-500/20 uppercase">Преподаватель</span>` : '';

                    const replyHtml = `
                        <div class="p-3 rounded-lg bg-slate-950/20 border border-white/5 flex gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-xs shrink-0 uppercase">
                                ${initials}
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-xs font-semibold text-slate-200">${userName}</span>
                                    ${teacherTag}
                                    <span class="text-[10px] text-slate-500">только что</span>
                                </div>
                                <p class="text-sm text-slate-300 break-words">${escapeHTML(reply.body)}</p>
                            </div>
                        </div>
                    `;
                    repliesList.insertAdjacentHTML('beforeend', replyHtml);

                    const countNumEl = item.querySelector('.replies-count-num');
                    if (countNumEl) {
                        countNumEl.textContent = parseInt(countNumEl.textContent) + 1;
                    }

                    if (data.is_answered) {
                        const badgeContainer = item.querySelector('.badge-answered-status');
                        if (badgeContainer) {
                            badgeContainer.innerHTML = `
                                <span class="px-2 py-0.5 text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 rounded-full border border-emerald-500/20">
                                    <i class="fa-solid fa-check me-1"></i>Решено
                                </span>
                            `;
                        }
                    }
                }
            })
            .catch(err => console.error('Error replying to discussion:', err));
        }
    });
});

// YouTube callback
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
                        const completeBtn = document.getElementById('mark-completed-btn');
                        if (completeBtn && !completeBtn.classList.contains('bg-emerald-600') && typeof window.toggleLessonProgress === 'function') {
                            const lessonId = completeBtn.getAttribute('data-lesson-id');
                            window.toggleLessonProgress(lessonId);
                        }
                    }
                }
            }
        });
    }
};
</script>
@endsection