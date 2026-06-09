@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <div class="page-header" style="margin-bottom: 40px;">
        <h1 class="page-title" style="font-size: 32px; background: linear-gradient(135deg, #fff 0%, var(--brand) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ __('messages.quizzes.title') }}</h1>
        <p style="color: var(--text-muted); margin-top: 8px;">{{ __('messages.quizzes.subtitle') }}</p>
    </div>

    <!-- Quizzes Table Card -->
    <div class="glass-card" style="overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.08);">
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_quiz') }}</th>
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_course') }}</th>
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_duration') }}</th>
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_passing_score') }}</th>
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_attempts') }}</th>
                    <th style="padding: 18px 24px; color: var(--text-muted); font-size: 13px; font-weight: 700; text-transform: uppercase;">{{ __('messages.quizzes.table_result') }}</th>
                    <th style="padding: 18px 24px; text-align: right;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $q)
                    @php
                        $lastAttempt = $q->attempts->first();
                    @endphp
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='none'">
                        <td style="padding: 18px 24px; font-weight: 600; color: #fff;">
                            {{ $q->title }}
                        </td>
                        <td style="padding: 18px 24px; color: var(--text-muted); font-size: 14px;">
                            {{ $q->course->title }}
                        </td>
                        <td style="padding: 18px 24px; color: var(--text-muted); font-size: 14px;">
                            {{ $q->duration_minutes ? __('messages.quizzes.param_minutes', ['minutes' => $q->duration_minutes]) : __('messages.quizzes.param_unlimited') }}
                        </td>
                        <td style="padding: 18px 24px; color: var(--text-muted); font-size: 14px;">
                            {{ $q->pass_percentage }}%
                        </td>
                        <td style="padding: 18px 24px; color: var(--text-muted); font-size: 14px;">
                            {{ $q->attempts->count() }} / {{ $q->max_attempts }}
                        </td>
                        <td style="padding: 18px 24px;">
                            @if(!$lastAttempt)
                                <span class="badge" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: var(--text-muted); font-size: 11px; padding: 4px 10px; border-radius: 8px;">{{ __('messages.quizzes.status_not_started') }}</span>
                            @elseif($lastAttempt->passed)
                                <span class="badge" style="background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.2); font-size: 11px; padding: 4px 10px; border-radius: 8px;">{{ __('messages.quizzes.status_passed') }} ({{ $lastAttempt->score }}%)</span>
                            @else
                                <span class="badge" style="background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); font-size: 11px; padding: 4px 10px; border-radius: 8px;">{{ __('messages.quizzes.status_failed') }} ({{ $lastAttempt->score }}%)</span>
                            @endif
                        </td>
                        <td style="padding: 18px 24px; text-align: right;">
                            <a href="{{ route('student.quizzes.show', $q->id) }}" class="btn {{ !$lastAttempt ? 'btn-gradient' : '' }}" style="{{ !$lastAttempt ? 'padding: 6px 14px; font-size: 12px; border-radius: 8px;' : 'padding: 6px 14px; font-size: 12px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; background: rgba(255, 255, 255, 0.02); transition: all 0.2s;' }}" onmouseover="{{ $lastAttempt ? 'this.style.background=\'rgba(255,255,255,0.08)\'; this.style.borderColor=\'rgba(255,255,255,0.3)\'' : '' }}" onmouseout="{{ $lastAttempt ? 'this.style.background=\'rgba(255,255,255,0.02)\'; this.style.borderColor=\'rgba(255,255,255,0.15)\'' : '' }}">
                                {{ !$lastAttempt ? __('messages.quizzes.btn_start') : __('messages.quizzes.btn_view_details') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            {{ __('messages.quizzes.empty_title') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection