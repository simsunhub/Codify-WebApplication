@extends('teacher.layouts.app')
@section('title', __('Course Reviews'))
@section('breadcrumb', __('Opinions'))

@section('content')

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-star me-2" style="color:var(--brand);"></i>{{ __('Students\' thoughts') }}-{{ __('opinions') }}</div>
            <div class="ed-card-subtitle">{{ __('Error 500 (Server Error)!!1500.That’s an error.There was an error. Please try again later.That’s all we know.') }}</div>
        </div>
        <div style="position:relative;max-width:240px;flex:1;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:12px;"></i>
            <input type="search" id="tableFilter" placeholder="{{ __('Look for…') }}"
                   style="width:100%;border:1.5px solid var(--card-border);border-radius:50px;padding:8px 14px 8px 34px;font-size:13px;outline:none;background:var(--card-bg2);color:var(--text);">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table" id="reviewsTable">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('A focused course') }}</th>
                    <th>{{ __('Rating') }} ({{ __('Price') }})</th>
                    <th>{{ __('Oops') }}-{{ __('opinion') }}</th>
                    <th>{{ __('On the date of submission') }}</th>
                </tr>
            </thead>
            <tbody id="reviewsTableBody">
            @forelse($reviews as $review)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $review->user->name ?? 'Student' }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $review->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="font-weight:700;">{{ $review->course->title ?? '—' }}</span>
                    </td>
                    <td>
                        <div style="color:var(--yellow);font-size:13.5px;letter-spacing:1px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                            <span class="text-white ms-1" style="font-weight:700;">({{ $review->rating }})</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;color:var(--text);line-height:1.5;max-width:320px;word-break:break-word;">{{ $review->comment ?? __('No comment left.') }}</div>
                    </td>
                    <td>
                        <div style="font-size:13px;color:var(--text-muted);">{{ $review->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">⭐</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('Oops') }}-{{ __('no comments') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('No students have rated your courses yet') }}.</div>
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
    const rows = document.querySelectorAll('#reviewsTableBody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection