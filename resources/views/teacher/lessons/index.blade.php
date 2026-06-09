@extends('teacher.layouts.app')
@section('title', __('My Lessons'))
@section('breadcrumb', __('My Lessons'))

@section('page-actions')
<a href="{{ route('teacher.lessons.create') }}" class="ed-btn ed-btn-primary">
    <i class="fas fa-plus"></i> {{ __('Add a lesson') }}
</a>
@endsection

@section('content')
<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title">
                <i class="fa-solid fa-play-circle me-2" style="color:var(--brand);"></i>{{ __('Your lessons') }}
            </div>
            <div class="ed-card-subtitle">Manage lessons and media content across all your courses</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>{{ __('Lesson name') }}</th>
                    <th>{{ __('messages.dash.course') }}</th>
                    <th>{{ __('Video') }}</th>
                    <th style="width: 100px;">{{ __('Order') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                <tr>
                    <td style="color:var(--text-muted); font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <strong style="color:var(--text);">{{ $lesson->title }}</strong>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo">
                            {{ $lesson->course->title ?? '-' }}
                        </span>
                    </td>
                    <td>
                        @if($lesson->video_url)
                            <span class="ed-badge ed-badge-green">
                                <i class="fas fa-video me-1"></i>{{ __('There is') }}
                            </span>
                        @else
                            <span class="ed-badge ed-badge-gray">
                                <i class="fas fa-video-slash me-1"></i>{{ __('Are not') }}
                            </span>
                        @endif
                    </td>
                    <td style="font-weight:600; color:var(--text-muted);">{{ $lesson->order }}</td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('teacher.lessons.edit', $lesson) }}" 
                               class="ed-action-btn ed-action-edit" 
                               title="Edit Lesson">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('teacher.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" 
                                        class="ed-action-btn ed-action-delete" 
                                        title="Delete Lesson"
                                        onclick="return confirm('{{ __('Turn it off?') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="font-size:40px; margin-bottom:14px;">🎬</div>
                        <div style="font-weight:700; color:var(--text); font-size:15px;">{{ __('There is no lesson') }}</div>
                        <div style="color:var(--text-muted); font-size:13px; margin:6px 0 18px;">Get started by adding your first lesson.</div>
                        <a href="{{ route('teacher.lessons.create') }}" class="ed-btn ed-btn-primary">
                            <i class="fas fa-plus"></i> {{ __('Add a lesson') }}
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection