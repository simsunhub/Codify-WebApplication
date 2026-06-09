@extends('layouts.app')

@section('title', $quiz->title . ' | ' . __('messages.quizzes.title'))

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <div style="margin-bottom: 30px;">
        <a href="{{ route('student.quizzes.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; background: rgba(255, 255, 255, 0.02); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'; this.style.borderColor='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'; this.style.borderColor='rgba(255,255,255,0.15)'">
            <i class="fas fa-arrow-left me-2"></i> {{ __('messages.quizzes.back_to_list') }}
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 16px; background: rgba(239, 68, 68, 0.1); color: #b91c1c;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Quiz Details Card -->
        <div class="col-lg-8">
            <div class="glass-card" style="padding: 32px;">
                <div style="margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 16px;">
                    <span style="font-size: 12px; font-weight: 700; color: #a5b4fc; text-transform: uppercase; letter-spacing: 0.5px;">{{ $quiz->course->title }}</span>
                    <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-top: 4px;">{{ $quiz->title }}</h1>
                </div>

                <div style="color: var(--text-primary); font-size: 15px; line-height: 1.6; margin-bottom: 28px;">
                    <h5 style="color: #fff; font-weight: 700; margin-bottom: 10px;">{{ __('messages.quizzes.about_title') }}</h5>
                    <p style="white-space: pre-wrap;">{{ $quiz->description ?? __('messages.quizzes.no_desc') }}</p>
                </div>

                <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px;">{{ __('messages.quizzes.history_title') }}</h3>
                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.04); border-radius: 12px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 14px 20px; color: var(--text-muted); font-size: 12.5px; font-weight: 700;">{{ __('messages.quizzes.table_date') }}</th>
                                <th style="padding: 14px 20px; color: var(--text-muted); font-size: 12.5px; font-weight: 700;">{{ __('messages.quizzes.table_score') }}</th>
                                <th style="padding: 14px 20px; color: var(--text-muted); font-size: 12.5px; font-weight: 700;">{{ __('messages.quizzes.table_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attempts as $attempt)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td style="padding: 14px 20px; color: var(--text-muted); font-size: 13.5px;">{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y H:i') : __('messages.quizzes.status_incomplete') }}</td>
                                    <td style="padding: 14px 20px; font-weight: 700; color: #fff;">{{ $attempt->score }}%</td>
                                    <td style="padding: 14px 20px;">
                                        @if($attempt->passed)
                                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); font-weight: 600; border-radius: 8px; padding: 4px 10px;">{{ __('messages.quizzes.status_passed') }}</span>
                                        @else
                                            <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 600; border-radius: 8px; padding: 4px 10px;">{{ __('messages.quizzes.status_failed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="padding: 24px; text-align: center; color: var(--text-muted); font-size: 13.5px;">{{ __('messages.quizzes.no_attempts_recorded') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quiz Information Sidebar -->
        <div class="col-lg-4">
            <div class="glass-card" style="padding: 32px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 12px;">
                    {{ __('messages.quizzes.parameters_title') }}
                </h3>

                <div style="display: flex; flex-direction: column; gap: 16px; font-size: 14px; margin-bottom: 28px;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">{{ __('messages.quizzes.param_questions') }}</span>
                        <strong style="color: #fff;">{{ $quiz->questions->count() }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">{{ __('messages.quizzes.param_pass_percentage') }}</span>
                        <strong style="color: #34d399;">{{ $quiz->pass_percentage }}%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 8px;">
                        <span style="color: var(--text-muted);">{{ __('messages.quizzes.param_attempts') }}</span>
                        <strong style="color: #fff;">{{ $attempts->count() }} / {{ $quiz->max_attempts }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 4px;">
                        <span style="color: var(--text-muted);">{{ __('messages.quizzes.param_time_limit') }}</span>
                        <strong style="color: #fff;">{{ $quiz->duration_minutes ? __('messages.quizzes.param_minutes', ['minutes' => $quiz->duration_minutes]) : __('messages.quizzes.param_unlimited') }}</strong>
                    </div>
                </div>

                @php
                    $hasPassed = $attempts->contains('passed', true);
                @endphp

                @if($hasPassed)
                    <button class="btn btn-sm" disabled style="padding: 12px; opacity: 0.9; cursor: not-allowed; border-radius: 10px; width: 100%; border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; background: rgba(16, 185, 129, 0.12); font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> {{ __('messages.quizzes.status_passed') }}
                    </button>
                @elseif($attempts->count() < $quiz->max_attempts)
                    <form action="{{ route('student.quizzes.start', $quiz->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-gradient w-100" style="padding: 12px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-weight: 700; border-radius: 10px;">
                            <i class="fas fa-play"></i> {{ __('messages.quizzes.btn_start_quiz') }}
                        </button>
                    </form>
                @else
                    <button class="btn btn-sm" disabled style="padding: 12px; opacity: 0.4; cursor: not-allowed; border-radius: 10px; width: 100%; border: 1px solid rgba(255, 255, 255, 0.1); color: var(--text-muted); background: rgba(255,255,255,0.02);">
                        {{ __('messages.quizzes.btn_no_attempts') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection