@extends('admin.layouts.app')

@section('title', __('Courses'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">{{ __('All courses') }}</h6>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>{{ __('Course basket') }}
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Course name') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('The teacher') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Level') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $course->title }}</strong>
                        <br><small class="text-muted">{{ Str::limit($course->description, 40) }}</small>
                    </td>
                    <td>{{ $course->category->name ?? '-' }}</td>
                    <td>{{ $course->user->name ?? '-' }}</td>
                    <td>${{ $course->price }}</td>
                    <td>
                        @if($course->level === 'beginner')
                            <span class="badge bg-success">{{ __('Beginner') }}</span>
                        @elseif($course->level === 'intermediate')
                            <span class="badge bg-warning text-dark">{{ __('Medium') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('High') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($course->is_active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('Turned off') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Turn it off?') }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">{{ __('There is no course') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $courses->links() }}</div>
@endsection