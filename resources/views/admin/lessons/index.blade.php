@extends('admin.layouts.app')

@section('title', __('Lessons'))

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">{{ __('All lessons') }}</h2>
        <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> {{ __('Add a lesson') }}
        </a>
    </div>

    <div class="table-container" style="border: none; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Lesson title') }}</th>
                    <th>{{ __('Well') }}</th>
                    <th>{{ __('Video') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessons as $lesson)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong style="color: var(--text-primary);">{{ $lesson->title }}</strong></td>
                    <td>{{ $lesson->course->title ?? '-' }}</td>
                    <td>
                        @if($lesson->video_url)
                            <span class="badge badge-success">
                                <i class="fas fa-video" style="margin-right: 4px;"></i>{{ __('Eat') }}
                            </span>
                        @else
                            <span class="badge badge-muted">{{ __('No') }}</span>
                        @endif
                    </td>
                    <td>{{ $lesson->order }}</td>
                    <td>
                        @if($lesson->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-muted">{{ __('Disabled') }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Delete lesson?') }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">{{ __('No lessons yet') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $lessons->links() }}</div>
@endsection