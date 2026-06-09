@extends('admin.layouts.app')
@section('title', __('Review the request'))
@section('content')
<div class="row g-4" style="display:flex; gap: 24px;">
    <div style="flex: 2;">
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <div>
                    <h2 class="card-title">{{ $ticket->subject }}</h2>
                    <small>{{ __('A student') }}: {{ $ticket->user->name ?? __('A student') }} | {{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                </div>
                <div>
                    @if($ticket->status !== 'closed')
                        <form action="{{ route('admin.support.close', $ticket->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm">{{ __('Close out') }} (Close)</button>
                        </form>
                    @endif
                    <a href="{{ route('admin.support.index') }}" class="btn btn-outline btn-sm">{{ __('Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <p style="white-space: pre-wrap; font-size:14.5px;">{{ $ticket->message }}</p>
            </div>
        </div>

        <h4 style="margin:20px 0 10px;">{{ __('Dialogue') }} ({{ __('Answers') }})</h4>
        @foreach($ticket->replies as $reply)
            <div class="card" style="margin-bottom: 12px; border-left: 3px solid {{ $reply->user->role === 'admin' ? 'var(--brand)' : '#ccc' }};">
                <div class="card-body" style="padding: 14px 18px;">
                    <div style="display:flex; justify-content:between; margin-bottom:8px; font-size:12px; color:var(--text-muted);">
                        <strong>{{ $reply->user->name }} ({{ strtoupper($reply->user->role) }})</strong>
                        <span>{{ $reply->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <p style="white-space: pre-wrap; margin:0;">{{ $reply->body }}</p>
                </div>
            </div>
        @endforeach

        @if($ticket->status !== 'closed')
            <div class="card" style="margin-top: 24px;">
                <div class="card-body">
                    <h5>{{ __('Write an answer') }}</h5>
                    <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 15px;">
                            <textarea name="body" rows="4" class="form-control" placeholder="{{ __('Write your answer here...') }}" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Send off') }}</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection