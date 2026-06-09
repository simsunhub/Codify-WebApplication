@extends('layouts.app')

@section('title', __('messages.quizzes.attempt_title') . ' | ' . $attempt->quiz->title)

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <div class="row g-4">
        <!-- Questions Form -->
        <div class="col-lg-8">
            <div class="glass-card" style="padding: 32px;">
                <div style="margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 16px;">
                    <span style="font-size: 12px; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.5px;">{{ $attempt->quiz->course->title }}</span>
                    <h1 style="font-size: 24px; font-weight: 800; color: #fff; margin-top: 4px;">{{ $attempt->quiz->title }}</h1>
                </div>

                <form id="quizForm" action="{{ route('student.quizzes.submit', $attempt->id) }}" method="POST">
                    @csrf
                    
                    @foreach($attempt->quiz->questions as $index => $q)
                        <div style="margin-bottom: 32px; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 24px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                <h4 style="font-size: 16px; font-weight: 700; color: #fff; margin: 0; line-height: 1.4;">
                                    {{ $index + 1 }}. {{ $q->question }}
                                </h4>
                                <span class="badge bg-secondary" style="font-size: 11px;">{{ $q->points }} {{ __('messages.quizzes.pts') }}</span>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @foreach($q->options as $option)
                                    <label style="display: flex; align-items: center; gap: 12px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 14px 18px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                                        <input type="radio" name="answers[{{ $q->id }}]" value="{{ $option->id }}" required style="accent-color: #6366f1;">
                                        <span style="color: #fff; font-size: 14.5px;">{{ $option->option_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <button type="submit" class="btn btn-gradient w-100" style="padding: 14px; font-size: 16px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 10px;" onclick="return confirm('{{ __('messages.quizzes.confirm_submit') }}')">
                        <i class="fas fa-paper-plane"></i> {{ __('messages.quizzes.btn_submit_quiz') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Timer Sidebar -->
        <div class="col-lg-4">
            <div class="glass-card" style="padding: 32px; text-align: center; position: sticky; top: 100px;">
                @if($remainingSeconds !== null)
                    <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('messages.quizzes.time_remaining') }}</div>
                    <div id="timer" style="font-size: 40px; font-weight: 800; color: #fff; font-family: monospace; line-height: 1; margin-bottom: 20px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 12px 20px; display: inline-block;">
                        00:00
                    </div>

                    <script>
                        var secondsLeft = {{ $remainingSeconds }};
                        var timerSpan = document.getElementById('timer');
                        var form = document.getElementById('quizForm');

                        function updateTimer() {
                            var minutes = Math.floor(secondsLeft / 60);
                            var seconds = secondsLeft % 60;

                            timerSpan.textContent = 
                                (minutes < 10 ? '0' + minutes : minutes) + ':' + 
                                (seconds < 10 ? '0' + seconds : seconds);

                            if (secondsLeft <= 0) {
                                clearInterval(interval);
                                alert('{{ __('messages.quizzes.alert_timeout') }}');
                                form.submit();
                            }
                            secondsLeft--;
                        }

                        updateTimer();
                        var interval = setInterval(updateTimer, 1000);
                    </script>
                @else
                    <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('messages.quizzes.param_time_limit') }}</div>
                    <div style="font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 20px;">
                        <i class="fa-solid fa-infinity"></i> {{ __('messages.quizzes.param_unlimited') }}
                    </div>
                @endif

                <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px; text-align: left; font-size: 13.5px; color: var(--text-muted);">
                    <i class="fa-solid fa-circle-question me-2"></i> {{ __('messages.quizzes.take_footer_note') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection