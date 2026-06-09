@extends('admin.layouts.app')
@section('title', __('Support tickets'))
@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('Technical Support Messages') }}</h2>
    </div>
    <div class="table-container" style="border: none;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('The subject') }}</th>
                    <th>{{ __('It\'s time') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td><strong>{{ $ticket->user->name ?? __('A student') }}</strong><br><small>{{ $ticket->user->email ?? '' }}</small></td>
                    <td><strong>{{ $ticket->subject }}</strong></td>
                    <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($ticket->status === 'closed')
                            <span class="badge badge-muted">{{ __('Closed') }}</span>
                        @elseif($ticket->status === 'replied')
                            <span class="badge badge-success">{{ __('Answered') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('Waiting') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.support.show', $ticket->id) }}" class="btn btn-outline btn-sm">{{ __('Look after') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('There are no tickets') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $tickets->links() }}</div>
@endsection