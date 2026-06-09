@extends('admin.layouts.app')
@section('title', __('Notices to teachers'))
@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">{{ __('Notices to teachers') }}</h2>
    </div>
    <div class="table-container" style="border: none; border-radius: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Name of the student') }}</th>
                    <th>{{ __('Direction') }}</th>
                    <th>{{ __('Application time') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $app->full_name }}</strong><br><small>{{ $app->email }}</small></td>
                    <td>{{ $app->expertise }}</td>
                    <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($app->status === 'approved')
                            <span class="badge badge-success">{{ __('Accepted') }}</span>
                        @elseif($app->status === 'rejected')
                            <span class="badge badge-danger">{{ __('Rejected') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('Waiting') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.teachers.show', $app->id) }}" class="btn btn-outline btn-sm">{{ __('View / Solution') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('There are no complaints') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $applications->links() }}</div>
@endsection