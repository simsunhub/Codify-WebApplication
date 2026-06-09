@extends('teacher.layouts.app')

@section('title', __('messages.announcements.announcements'))

@section('content')
<div class="ed-page-header mb-4">
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
            <h1 class="ed-page-title">
                <i class="fa-solid fa-bullhorn me-2" style="color:var(--brand)"></i>
                {{ __('messages.announcements.announcements') }}
            </h1>
            <p class="ed-page-sub">{{ __('messages.announcements.teacher_subtitle') }}</p>
        </div>
        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-primary btn-sm px-4 rounded-pill">
            <i class="fa-solid fa-plus me-1"></i> {{ __('messages.announcements.new_announcement') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($announcements->isEmpty())
    <div class="text-center py-5" style="color:var(--text-muted)">
        <i class="fa-solid fa-bullhorn fa-3x mb-3 opacity-25"></i>
        <p class="fs-6">{{ __('messages.announcements.no_announcements') }}</p>
        <a href="{{ route('teacher.announcements.create') }}" class="btn btn-outline-primary btn-sm mt-2 rounded-pill">
            {{ __('messages.announcements.new_announcement') }}
        </a>
    </div>
@else
    <div class="row g-3">
        @foreach($announcements as $ann)
        <div class="col-12">
            <div class="ann-card">
                <div class="ann-card-icon">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="ann-card-body">
                    <div class="ann-card-meta">
                        <span class="ann-badge badge-course">
                            <i class="fa-solid fa-graduation-cap me-1"></i>
                            {{ $ann->course->title ?? __('messages.announcements.all_courses') }}
                        </span>
                        <span class="ann-card-date">{{ $ann->created_at->diffForHumans() }}</span>
                    </div>
                    <h5 class="ann-card-title">{{ $ann->title }}</h5>
                    <p class="ann-card-text">{{ Str::limit($ann->content, 200) }}</p>
                </div>
                <div class="ann-card-actions">
                    <a href="{{ route('teacher.announcements.edit', $ann) }}"
                       class="btn-ann-action btn-ann-edit" title="{{ __('messages.dash.edit') }}">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                    <form action="{{ route('teacher.announcements.destroy', $ann) }}" method="POST"
                          onsubmit="return confirm('{{ __('messages.announcements.confirm_delete') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-ann-action btn-ann-delete" title="{{ __('messages.dash.delete') }}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
@endif

<style>
.ann-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    transition: var(--transition);
}
.ann-card:hover {
    border-color: var(--brand);
    box-shadow: 0 0 0 1px var(--brand), var(--card-shadow-h);
}
.ann-card-icon {
    width: 44px; height: 44px;
    border-radius: var(--radius-md);
    background: var(--brand-light);
    color: var(--brand);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.ann-card-body { flex: 1; min-width: 0; }
.ann-card-meta { display: flex; align-items: center; gap: .75rem; margin-bottom: .5rem; }
.ann-badge {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem;
    border-radius: var(--radius-full);
    font-size: .72rem;
    font-weight: 600;
    letter-spacing: .02em;
}
.badge-course { background: rgba(99,102,241,.15); color: #818cf8; }
.ann-card-date { font-size: .76rem; color: var(--text-muted); margin-left: auto; }
.ann-card-title { font-size: .95rem; font-weight: 700; color: var(--text); margin-bottom: .35rem; }
.ann-card-text { font-size: .83rem; color: var(--text-muted); margin: 0; }
.ann-card-actions { display: flex; flex-direction: column; gap: .4rem; flex-shrink: 0; }
.btn-ann-action {
    width: 34px; height: 34px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--card-border);
    background: transparent;
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: .85rem;
    transition: var(--transition);
    text-decoration: none;
}
.btn-ann-edit:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-light); }
.btn-ann-delete:hover { border-color: var(--red); color: var(--red); background: rgba(239,68,68,.1); }
</style>
@endsection
