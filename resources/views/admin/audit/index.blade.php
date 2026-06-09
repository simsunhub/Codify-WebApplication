@extends('admin.layouts.app')
@section('title', __('Security and Audit Logs'))
@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">{{ __('User activity in the system') }}-{{ __('actions') }}</h2>
    </div>
    <div class="table-container" style="border: none;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('The user') }}</th>
                    <th>{{ __('Action') }}</th>
                    <th>{{ __('Table/Model') }}</th>
                    <th>IP-{{ __('address') }}</th>
                    <th>{{ __('The date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><strong>{{ $log->user->name ?? __('Guest') }}</strong><br><small>{{ $log->user->email ?? '' }}</small></td>
                    <td><code>{{ strtoupper($log->action) }}</code></td>
                    <td>{{ $log->auditable_type }} (ID: {{ $log->auditable_id }})</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">{{ __('The work') }}-{{ __('actions have not been registered') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div>{{ $logs->links() }}</div>
@endsection