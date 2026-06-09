@extends('teacher.layouts.app')
@section('title', __('View and answer the question'))
@section('breadcrumb', __('Question-Answer Thread'))

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(16,185,129,.1);color:var(--green);border:1px solid rgba(16,185,129,.2);border-radius:var(--radius-md);">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        
        <h5 style="font-weight:800;color:var(--text);margin:28px 0 16px;"><i class="fa-solid fa-comments me-2" style="color:var(--brand);"></i>{{ __('Answers related to the topic') }} ({{ $discussion->replies->count() }})</h5>

        @forelse($discussion->replies as $reply)
            <div class="ed-card mb-3" style="@if($reply->is_answer) border:1px solid var(--green); background:rgba(16,185,129,.02); @endif">
                <div class="ed-card-body" style="padding:16px 20px;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12.5px;
                                        @if($reply->user->role === 'instructor')
                                            background:var(--brand);color:#fff;
                                        @else
                                            background:rgba(255,255,255,.05);color:var(--text-muted);
                                        @endif">
                                {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13.5px;color:var(--text);">
                                    {{ $reply->user->name }}
                                    @if($reply->user->role === 'instructor')
                                        <span class="badge bg-danger ms-1" style="font-size:9.5px;padding:3px 6px;">{{ __('THE TEACHER') }}</span>
                                    @endif
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);">{{ $reply->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>

                        @if($reply->is_answer)
                            <span class="ed-badge ed-badge-green"><i class="fa-solid fa-check-double me-1"></i> {{ __('Selected as the main answer') }}</span>
                        @endif
                    </div>

                    <div style="font-size:13.8px;color:var(--text);line-height:1.6;white-space:pre-wrap;padding-left:44px;">{{ $reply->body }}</div>
                </div>
            </div>
        @empty
            <div class="ed-card text-center py-4 mb-4">
                <div class="text-muted" style="font-size:13px;">{{ __('No answers yet') }}. {{ __('Error 500 (Server Error)!!1500.That’s an error.There was an error. Please try again later.That’s all we know.') }}!</div>
            </div>
        @endforelse

        {{-- Write reply --}}
        <div class="ed-card mt-5">
            <div class="ed-card-header">
                <div class="ed-card-title"><i class="fa-solid fa-reply me-2" style="color:var(--brand);"></i>{{ __('Respond to the student') }}</div>
            </div>
            <div class="ed-card-body">
                <form action="{{ route('teacher.discussions.reply', $discussion->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <textarea name="body" rows="5" class="form-control" placeholder="{{ __('Please write the full text of your answer here...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_answer" id="isAnswerCheck" style="transform:scale(1.2);accent-color:var(--brand);cursor:pointer;">
                            <label class="form-check-label ms-2" for="isAnswerCheck" style="font-weight:600;color:var(--text);cursor:pointer;">
                                {{ __('Mark this answer as the main/correct solution to the problem') }}
                            </label>
                            <div class="form-text ms-2" style="color:var(--text-muted);font-size:11.5px;">{{ __('Ask the question') }}-{{ __('The answer will be marked as solved and the student will be notified') }}.</div>
                        </div>
                    </div>

                    <button type="submit" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
                        <i class="fa-solid fa-paper-plane"></i> {{ __('Send a reply') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection