@extends('admin.layouts.app')

@section('title', __('messages.dash.announcements'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">{{ __('messages.dash.all_announcements') }}</h6>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('messages.dash.add_announcement') }}
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.dash.title_field') }}</th>
                    <th>{{ __('messages.announcements.target_audience') }}</th>
                    <th>{{ __('messages.dash.content_field') }}</th>
                    <th>{{ __('messages.dash.status_field') }}</th>
                    <th>{{ __('messages.dash.stats.date') }}</th>
                    <th>{{ __('messages.dash.action_field') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>
                        @if($announcement->target_role == 'all')
                            <span class="badge bg-info">🌐 {{ __('messages.announcements.target_all') }}</span>
                        @elseif($announcement->target_role == 'student_only')
                            <span class="badge bg-primary">🎓 {{ __('messages.announcements.target_students') }}</span>
                        @elseif($announcement->target_role == 'teacher_only')
                            <span class="badge bg-warning text-dark">👨‍🏫 {{ __('messages.announcements.target_teachers') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $announcement->target_role }}</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($announcement->content, 50) }}</td>
                    <td>
                        @if($announcement->is_active)
                            <span class="badge bg-success">{{ __('messages.dash.active_status') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('messages.dash.disabled_status') }}</span>
                        @endif
                    </td>
                    <td>{{ $announcement->created_at->format('d.m.Y') }}</td>
                    <td>
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.dash.confirm_delete_q') }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">{{ __('messages.dash.no_announcements') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $announcements->links() }}</div>
@endsection
