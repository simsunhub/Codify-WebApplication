@extends('teacher.layouts.app')
@section('title', __('Test Results'))
@section('breadcrumb', __('Test Results'))

@section('content')

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="ed-card" style="border-left:4px solid var(--green);">
            <div class="ed-card-body" style="padding:22px 26px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">{{ __('Test results') }}</div>
                        <h4 style="font-weight:800;color:var(--text);margin:6px 0 4px;">{{ $quiz->title }}</h4>
                        <div style="font-size:13px;color:var(--text-muted);">
                            {{ __('Passing Threshold') }}: <strong style="color:var(--yellow);">{{ $quiz->pass_percentage }}%</strong> | 
                            {{ __('Total number of submitted attempts') }}: <span class="badge bg-dark">{{ $attempts->count() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.quizzes.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('Back to tests') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-square-poll-vertical me-2" style="color:var(--brand);"></i>{{ __('Action list') }}</div>
            <div class="ed-card-subtitle">{{ __('Student\'s efforts on this test') }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('Start time') }}</th>
                    <th>{{ __('End time') }}</th>
                    <th>{{ __('Score') }} (%)</th>
                    <th>{{ __('Condition') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($attempts as $attempt)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $attempt->user->name ?? 'Student' }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $attempt->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;color:var(--text);">{{ $attempt->started_at ? $attempt->started_at->format('d.m.Y H:i') : '—' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;color:var(--text);">{{ $attempt->completed_at ? $attempt->completed_at->format('d.m.Y H:i') : __('Not finished yet') }}</div>
                    </td>
                    <td>
                        <div style="font-size:14.5px;font-weight:800;color:var(--text);">
                            {{ $attempt->score }}% 
                            <span style="font-size:11px;font-weight:500;color:var(--text-muted);">({{ $attempt->correct_answers_count }} / {{ $attempt->total_questions_count }})</span>
                        </div>
                    </td>
                    <td>
                        @if($attempt->is_passed)
                            <span class="ed-badge ed-badge-green">{{ __('Passed') }}</span>
                        @else
                            <span class="ed-badge ed-badge-red">{{ __('There is no past') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📊</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('No actions') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('No student has taken this test yet') }}.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection