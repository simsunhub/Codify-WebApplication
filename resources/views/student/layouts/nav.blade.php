@php
    $user = auth()->user();
    // Try to auto-detect course if not explicitly passed
    $activeCourse = isset($course) ? $course : null;
    if (!$activeCourse) {
        if (request()->route('course')) {
            $courseVal = request()->route('course');
            if ($courseVal instanceof \App\Models\Course) {
                $activeCourse = $courseVal;
            } elseif (is_numeric($courseVal)) {
                $activeCourse = \App\Models\Course::find($courseVal);
            } else {
                $activeCourse = \App\Models\Course::where('slug', $courseVal)->first();
            }
        } elseif (request()->route('slug')) {
            $activeCourse = \App\Models\Course::where('slug', request()->route('slug'))->first();
        }
    }
@endphp

<div class="student-nav-container">
    <div class="student-nav-pill flex flex-row flex-nowrap md:flex-wrap items-center md:justify-center gap-4 w-full max-w-7xl mx-auto px-4 overflow-x-auto scrollbar-none whitespace-nowrap">
        
        {{-- 1. My Learning --}}
        @if(\App\Models\LmsModule::isVisible('my_learning', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('my_learning', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('my-learning') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('my-learning') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.learning.title')]) }}'); return false;" @endif>
                <i class="fas fa-graduation-cap"></i>
                <span>{{ __('messages.learning.title') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 2. Practice --}}
        @if(\App\Models\LmsModule::isVisible('practice', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('practice', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.coding.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.coding.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.practice')]) }}'); return false;" @endif>
                <i class="fas fa-code"></i>
                <span>{{ __('messages.dash.practice') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 3. Assignments --}}
        @if(\App\Models\LmsModule::isVisible('assignments', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('assignments', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.assignments.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.assignments.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.assignments')]) }}'); return false;" @endif>
                <i class="fas fa-tasks"></i>
                <span>{{ __('messages.dash.assignments') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 4. Quizzes --}}
        @if(\App\Models\LmsModule::isVisible('quizzes', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('quizzes', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.quizzes.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.quizzes.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.quizzes')]) }}'); return false;" @endif>
                <i class="fas fa-question-circle"></i>
                <span>{{ __('messages.dash.quizzes') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 5. Wishlist --}}
        @if(\App\Models\LmsModule::isVisible('wishlist', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('wishlist', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('wishlist.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('wishlist.index') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.wishlist')]) }}'); return false;" @endif>
                <i class="fas fa-heart"></i>
                <span>{{ __('messages.dash.wishlist') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 6. Messages --}}
        @if(\App\Models\LmsModule::isVisible('messages', $activeCourse))
            @php 
                $isLocked = \App\Models\LmsModule::isLocked('messages', $user);
                $unreadMessagesCount = \App\Models\Message::where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            @endphp
            <a href="{{ $isLocked ? '#' : route('messages.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('messages.index') || request()->routeIs('messages.show') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.messages')]) }}'); return false;" @endif>
                <i class="fas fa-comment-alt"></i>
                <span>{{ __('messages.dash.messages') }}</span>
                @if(!$isLocked && $unreadMessagesCount > 0)
                    <span class="badge-unread" style="display: inline-flex; align-items: center; justify-content: center; background: #ef4444; color: #fff; font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px; margin-left: 6px; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);">
                        {{ $unreadMessagesCount }}
                    </span>
                @endif
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 7. Certificates --}}
        @if(\App\Models\LmsModule::isVisible('certificates', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('certificates', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('certificates') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('certificates') || request()->routeIs('certificates.show') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.certificates')]) }}'); return false;" @endif>
                <i class="fas fa-award"></i>
                <span>{{ __('messages.dash.certificates') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 8. Purchases --}}
        @if(\App\Models\LmsModule::isVisible('purchases', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('purchases', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.orders.index') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.orders.*') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.purchases')]) }}'); return false;" @endif>
                <i class="fas fa-shopping-bag"></i>
                <span>{{ __('messages.dash.purchases') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 9. Playlist --}}
        @if(\App\Models\LmsModule::isVisible('playlist', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('playlist', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.playlist') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.playlist') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.playlist')]) }}'); return false;" @endif>
                <i class="fas fa-list"></i>
                <span>{{ __('messages.dash.playlist') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 10. Watch Later --}}
        @if(\App\Models\LmsModule::isVisible('watch_later', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('watch_later', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('student.watch-later') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('student.watch-later') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.watch_later')]) }}'); return false;" @endif>
                <i class="fas fa-clock"></i>
                <span>{{ __('messages.dash.watch_later') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

        {{-- 11. Profile --}}
        @if(\App\Models\LmsModule::isVisible('profile', $activeCourse))
            @php $isLocked = \App\Models\LmsModule::isLocked('profile', $user); @endphp
            <a href="{{ $isLocked ? '#' : route('profile.edit') }}" 
               class="student-nav-item px-5 py-3 {{ request()->routeIs('profile.edit') ? 'active text-white font-semibold' : 'text-slate-300 hover:text-white' }}"
               @if($isLocked) onclick="alert('{{ __('messages.admin.modules.premium_alert', ['module' => __('messages.dash.profile')]) }}'); return false;" @endif>
                <i class="fas fa-user-cog"></i>
                <span>{{ __('messages.dash.profile') }}</span>
                @if($isLocked)
                    <i class="fas fa-lock text-warning ms-1" style="font-size: 11px !important; margin-left: 4px !important;"></i>
                @endif
            </a>
        @endif

    </div>
</div>

<style>
    .student-nav-container {
        width: 100%;
        margin-top: -28px !important; /* Pull up to align perfectly with the grid (24px gap) */
        margin-bottom: 32px;
        position: relative;
        z-index: 10;
        animation: studentNavFadeIn 0.5s ease;
    }

    .student-nav-pill {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important; /* Allow wrapping on desktop/tablet so they go to second row */
        justify-content: center !important; /* Center the buttons */
        gap: 16px !important;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px) saturate(120%);
        -webkit-backdrop-filter: blur(16px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px !important;
        padding: 8px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        width: 100% !important;
        max-width: 80rem !important; /* max-w-7xl */
        margin: 0 auto !important;
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .scrollbar-none {
        scrollbar-width: none !important;
    }
    .scrollbar-none::-webkit-scrollbar {
        display: none !important;
    }

    .student-nav-item {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 12px 20px !important; /* px-5 py-3 */
        color: #cbd5e1 !important; /* text-slate-300 */
        font-size: 13px !important;
        border-radius: 14px !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative !important;
        text-decoration: none !important;
        flex: 0 0 auto !important; /* Strictly no shrinking and no growing */
        width: auto !important;
        white-space: nowrap !important; /* Keep text on one line */
    }

    .student-nav-item i {
        font-size: 14px !important;
        color: #cbd5e1 !important;
        transition: transform 0.25s ease !important;
    }

    .student-nav-item:hover {
        color: #ffffff !important; /* hover:text-white */
        background: rgba(255, 255, 255, 0.05) !important;
    }

    .student-nav-item:hover i {
        color: #ffffff !important;
        transform: translateY(-1px) scale(1.05) !important;
    }

    .student-nav-item.active {
        color: #ffffff !important; /* text-white */
        font-weight: 600 !important; /* font-semibold */
        background: linear-gradient(135deg, var(--brand, #6366f1), var(--brand-dark, #4f46e5)) !important;
        box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.4) !important;
    }

    .student-nav-item.active i,
    .student-nav-item.active span {
        color: #ffffff !important;
    }

    /* Horizontal scroll/styling overrides for mobile devices */
    @media (max-width: 768px) {
        .student-nav-container {
            margin-top: -16px !important; /* Slightly less negative margin on mobile screens */
            margin-bottom: 24px;
            padding: 0 4px;
        }

        .student-nav-pill {
            flex-wrap: nowrap !important; /* Scroll instead of wrapping on mobile */
            max-width: 100% !important;
            overflow-x: auto !important;
            justify-content: flex-start !important;
            border-radius: 20px !important;
            padding: 8px !important;
        }
    }

    @keyframes studentNavFadeIn {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

</style>