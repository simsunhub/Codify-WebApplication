@for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($rating))
                                    <i class="fas fa-star"></i>
                                @elseif($i - $rating < 1 && $i - $rating > 0)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="count">({{ number_format($reviewCount) }} {{ Str::plural('rating', $reviewCount) }})</span>
                    </div>
                @endif
                <span class="meta-enrolled">
                    <i class="fas fa-users"></i>
                    {{ number_format($enrollCount) }} enrolled
                </span>
            </div>

            {{-- Instructor line --}}
            @if($course->instructor)
                <div class="hero-instructor">
                    @if($course->instructor->avatar)
                        <img src="{{ Storage::url($course->instructor->avatar) }}" alt="{{ $course->instructor->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->instructor->name) }}&background=3B82F6&color=fff&size=76" alt="{{ $course->instructor->name }}">
                    @endif
                    <span>Created by <a href="#">{{ $course->instructor->name }}</a></span>
                </div>
            @endif

            {{-- Progress for enrolled students --}}
            @if($isEnrolled && $course->lessons->count() > 0)
                @php
                    $totalLessons = $course->lessons->count();
                    $completedCount = count($completedLessons);
                    $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                @endphp
                <div class="hero-progress">
                    <div class="hero-progress-label">
                        <span>Your Progress</span>
                        <strong>{{ $progressPct }}% complete</strong>
                    </div>
                    <div class="progress-wrap" style="height:8px;">
                        <div class="progress-bar" style="width:{{ $progressPct }}%;"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Card --}}
        <div class="course-sidebar">
            <div class="sidebar-card">
                <div class="sidebar-img">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}">
                    @else
                        <div style="width:100%;height:100%;background:var(--card-bg2);display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-play-circle" style="font-size:48px;color:var(--text-muted);"></i>
                        </div>
                    @endif
                    <div class="play-overlay">
                        <span class="play-circle"><i class="fas fa-play" style="margin-left:3px;"></i></span>
                    </div>
                </div>
                <div class="sidebar-body">
                    <div class="price-row">
                        @if($course->price > 0)
                            <span class="price-current">${{ number_format($course->price, 2) }}</span>
                        @else
                            <span class="price-free">Free</span>
                        @endif
                    </div>

                    @if($isEnrolled)
                        <a href="{{ route('course.show', $course->slug) }}" class="btn-enroll btn-enroll-continue">
                            <i class="fas fa-play"></i> Continue Learning
                        </a>
                    @else
                        @auth
                            <form action="{{ route('course.enroll', $course->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-enroll btn-enroll-primary">
                                    <i class="fas fa-graduation-cap"></i> Enroll Now
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn-enroll btn-enroll-primary">
                                <i class="fas fa-graduation-cap"></i> Enroll Now
                            </a>
                        @endauth
                    @endif

                    <p class="sidebar-guarantee">
                        <i class="fas fa-shield-alt"></i> 30-Day Money-Back Guarantee
                    </p>

                    <div class="sidebar-features">
                        <div class="sidebar-feature">
                            <i class="fas fa-list-ul"></i>
                            {{ $course->lessons->count() }} {{ Str::plural('lesson', $course->lessons->count()) }}
                        </div>
                        <div class="sidebar-feature">
                            <i class="fas fa-infinity"></i>
                            Full lifetime access
                        </div>
                        <div class="sidebar-feature">
                            <i class="fas fa-mobile-alt"></i>
                            Access on mobile and desktop
                        </div>
                        <div class="sidebar-feature">
                            <i class="fas fa-certificate"></i>
                            Certificate of completion
                        </div>
                        <div class="sidebar-feature">
                            <i class="fas fa-language"></i>
                            English language
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{--