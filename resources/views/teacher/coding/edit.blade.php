av-icon"><i class="fa-solid fa-circle-question"></i></span>
            {{ __('messages.dash.manage_quizzes') }}
        </a>

        {{-- Students --}}
        <a href="{{ route('teacher.students.index') }}"
           class="ed-nav-link {{ request()->routeIs('teacher.students.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
            {{ __('messages.dash.students_list') }}
        </a>

        {{-- Discussions Q&A --}}
        <a href="{{ route('teacher.discussions.index') }}"
           class="ed-nav-link {{ request()->routeIs('teacher.discussions.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-comments"></i></span>
            {{ __('messages.dash.discussions_qa') }}
        </a>

        {{-- Reviews --}}
        <a href="{{ route('teacher.reviews.index') }}"
           class="ed-nav-link {{ request()->routeIs('teacher.reviews.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-star"></i></span>
            {{ __('messages.dash.reviews') }}
        </a>

        {{-- Revenue --}}
        <a href="{{ route('teacher.revenue.index') }}"
           class="ed-nav-link {{ request()->routeIs('teacher.revenue.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-wallet"></i></span>
            {{ __('messages.dash.revenue_payments') }}
        </a>

        <div class="ed-nav-label mt-2">{{ __('messages.dash.account') }}</div>

        {{-- Profile --}}
        <a href="{{ route('profile.edit') }}"
           class="ed-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-user-pen"></i></span>
            {{ __('messages.dash.profile') }}
        </a>

        {{-- View Site --}}
        <a href="{{ route('home') }}" class="ed-nav-link">
            <span class="nav-icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
            {{ __('messages.dash.view_site') }}
        </a>
    </nav>

    {{-- Footer user card --}}
    <div class="ed-sidebar-footer">
        <div class="ed-user-mini">
            @if($__user->avatar)
                <img src="{{ Storage::url($__user->avatar) }}" class="ed-user-avatar-sm" style="object-fit: cover;" alt="Avatar">
            @else
                <div class="ed-user-avatar-sm">{{ strtoupper(substr($__user->name ?? 'I', 0, 1)) }}</div>
            @endif
            <div>
                <div class="ed-user-mini-name">{{ $__user->name ?? 'Instructor' }}</div>
                <div class="ed-user-mini-role">{{ __('messages.dash.instructor') }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="ed-nav-link" style="margin-bottom:0;">
                <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                {{ __('messages.dash.logout') }}
            </button>
        </form>
    </div>
</aside>

{{-- Mobile backdrop --}}
<div class="ed-backdrop" id="edBackdrop"></div>

{{--