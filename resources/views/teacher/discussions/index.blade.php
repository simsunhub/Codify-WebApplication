@extends('teacher.layouts.app')
@section('title', __('Forum / Questions and Answers'))
@section('breadcrumb', __('Question-Answer'))

@section('content')

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-comments me-2" style="color:var(--brand);"></i>{{ __('Forum questions') }}</div>
            <div class="ed-card-subtitle">{{ __('A list of questions asked by your students as part of the course') }}</div>
        </div>
        <div style="position:relative;max-width:240px;flex:1;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:12px;"></i>
            <input type="search" id="tableFilter" placeholder="{{ __('Look for…') }}"
                   style="width:100%;border:1.5px solid var(--card-border);border-radius:50px;padding:8px 14px 8px 34px;font-size:13px;outline:none;background:var(--card-bg2);color:var(--text);">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table" id="discussionsTable">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('Topic / Question') }}</th>
                    <th>{{ __('Well') }}</th>
                    <th>{{ __('To the author') }}</th>
                    <th>{{ __('Answers') }}</th>
                    <th>{{ __('Resolution') }}</th>
                    <th class="text-end" style="width:140px;">{{ __('Answer') }}</th>
                </tr>
            </thead>
            <tbody id="discussionsTableBody">
            @forelse($discussions as $disc)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $disc->title }}</div>
                        @if($disc->body)
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ \Illuminate\Support\Str::limit($disc->body, 60) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="font-weight:700;">{{ $disc->course->title ?? '—' }}</span>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text);">{{ $disc->user->name ?? 'Student' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $disc->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="background:rgba(99, 102, 241,.1);color:var(--brand);font-weight:700;">
                            <i class="fa-solid fa-reply me-1"></i>
                            {{ $disc->replies_count ?? 0 }} {{ __('the answer') }}
                        </span>
                    </td>
                    <td>
                        @if($disc->is_answered)
                            <span class="ed-badge ed-badge-green"><i class="fa-solid fa-check me-1"></i> {{ __('It was decided') }}</span>
                        @else
                            <span class="ed-badge ed-badge-yellow"><i class="fa-solid fa-clock me-1"></i> {{ __('Awaiting response') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('teacher.discussions.show', $disc->id) }}"
                           class="ed-btn btn-sm" style="background:var(--brand);color:#fff;border:0;padding:5px 12px;font-size:12.5px;text-decoration:none;">
                            <i class="fa-solid fa-reply me-1"></i> {{ __('Log on') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">💬</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('messages.dash.no_discussions') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('messages.dash.no_discussions_desc') }}</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.getElementById('tableFilter')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#discussionsTableBody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection