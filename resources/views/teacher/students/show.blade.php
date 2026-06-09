@extends('teacher.layouts.app')
@section('title', __('Student Profile'))
@section('breadcrumb', __('Student Profiles'))

@section('content')

<div class="row g-4">
    <!-- Left Column: Student Personal Info -->
    <div class="col-lg-4">
        <div class="ed-card text-center" style="background:var(--card-bg);border:1px solid var(--card-border);border-radius:var(--radius-md);padding:24px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(99, 102, 241, 0.12); color: var(--brand); font-size: 32px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; border: 2px solid var(--card-border);">
                {{ strtoupper(substr($student->name, 0, 2)) }}
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 4px;">{{ $student->name }}</h3>
            <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 16px;">{{ $student->email }}</p>
            
            <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--card-border); padding-top: 16px; text-align: left;">
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted);">{{ __('Role') }}</span>
                    <span class="ed-badge ed-badge-indigo" style="text-transform: capitalize;">{{ __($student->role) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted);">{{ __('Registered on') }}</span>
                    <span style="color: var(--text); font-weight: 600;">{{ $student->created_at ? $student->created_at->format('d.m.Y') : '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted);">{{ __('Enrolled courses') }}</span>
                    <span style="color: var(--text); font-weight: 600;">{{ $enrollments->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Course Progress Table -->
    <div class="col-lg-8">
        <div class="ed-card" style="background:var(--card-bg);border:1px solid var(--card-border);border-radius:var(--radius-md);">
            <div class="ed-card-header" style="border-bottom: 1px solid var(--card-border);">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-graduation-cap me-2" style="color:var(--brand);"></i>{{ __('Course progress') }}</div>
                    <div class="ed-card-subtitle">{{ __('Student activity and attendance rates in your courses') }}</div>
                </div>
            </div>

            <div class="ed-card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table ed-table m-0">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">{{ __('Name of the course') }}</th>
                                <th>{{ __('Date of registration') }}</th>
                                <th>{{ __('Level of mastery') }}</th>
                                <th class="text-end" style="padding-right: 24px;">{{ __('Achievement') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($enrollments as $enrollment)
                            @php
                                $lessonsCount = $enrollment->course->lessons->count();
                                $completedCount = $lessonsCount > 0 ? \App\Models\LessonProgress::where('user_id', $student->id)->whereIn('lesson_id', $enrollment->course->lessons->pluck('id'))->count() : 0;
                                $progressPercent = $lessonsCount > 0 ? round(($completedCount / $lessonsCount) * 100) : 0;
                            @endphp
                            <tr>
                                <td style="padding-left: 24px;">
                                    <div style="font-weight:700;color:var(--text);">{{ $enrollment->course->title }}</div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ __('Total number of lessons') }}: {{ $lessonsCount }}</div>
                                </td>
                                <td>
                                    <span style="font-size:13.5px;color:var(--text);">{{ $enrollment->created_at ? $enrollment->created_at->format('d.m.Y') : ($enrollment->pivot->enrolled_at ? \Carbon\Carbon::parse($enrollment->pivot->enrolled_at)->format('d.m.Y') : '—') }}</span>
                                </td>
                                <td style="vertical-align:middle;min-width:180px;">
                                    <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;display:flex;justify-content:space-between;">
                                        <span>{{ __('Progress') }}</span>
                                        <span style="font-weight: 700; color: var(--text);">{{ $progressPercent }}%</span>
                                    </div>
                                    <div class="progress" style="height:6px;background:rgba(255,255,255,.05);border-radius:10px;overflow:hidden;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progressPercent }}%; background:var(--brand); border-radius:10px;" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td class="text-end" style="vertical-align:middle; padding-right: 24px;">
                                    @if($progressPercent === 100)
                                        <span class="ed-badge ed-badge-green"><i class="fa-solid fa-certificate me-1"></i> {{ __('It\'s over') }}</span>
                                    @else
                                        <span class="ed-badge ed-badge-yellow"><i class="fa-solid fa-circle-play me-1"></i> {{ __('He is reading') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div style="font-size:40px;margin-bottom:14px;">📚</div>
                                    <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('No courses found') }}</div>
                                    <div style="color:var(--text-muted);font-size:13px;">{{ __('This student is not enrolled in any of your courses.') }}</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection