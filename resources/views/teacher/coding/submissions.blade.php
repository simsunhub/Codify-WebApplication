@extends('teacher.layouts.app')
@section('title', __('Code Solutions'))
@section('breadcrumb', __('Problem Solutions'))

@section('content')

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="ed-card" style="border-left:4px solid var(--brand);">
            <div class="ed-card-body" style="padding:22px 26px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">{{ __('Encoding issue submissions') }}</div>
                        <h4 style="font-weight:800;color:var(--text);margin:6px 0 4px;">{{ $problem->title }}</h4>
                        <div style="font-size:13px;color:var(--text-muted);">
                            {{ __('Difficulty') }}: 
                            @if($problem->difficulty === 'easy')
                                <span class="badge bg-success">{{ __('Easy') }}</span>
                            @elseif($problem->difficulty === 'medium')
                                <span class="badge bg-warning text-dark">{{ __('Medium') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Difficult') }}</span>
                            @endif
                            | {{ __('Commonly shipped solutions') }}: <span class="badge bg-dark">{{ $submissions->count() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.coding.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('Back to the issues') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-paper-plane me-2" style="color:var(--brand);"></i>{{ __('Student decisions') }}</div>
            <div class="ed-card-subtitle">{{ __('A list of students trying to solve the problem') }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('Time sent') }}</th>
                    <th>{{ __('Language') }}</th>
                    <th>{{ __('Passing tests') }}</th>
                    <th>{{ __('Condition') }} ({{ __('Status') }})</th>
                    <th class="text-end" style="width:120px;">{{ __('View the code') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($submissions as $sub)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $sub->user->name ?? 'Student' }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $sub->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;color:var(--text);">{{ $sub->submitted_at ? $sub->submitted_at->format('d.m.Y H:i') : $sub->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="font-weight:700;">{{ $sub->language->name ?? '—' }}</span>
                    </td>
                    <td>
                        <strong style="color:var(--green);font-size:14px;">{{ $sub->passed_test_cases }}</strong> / {{ $sub->total_test_cases }}
                    </td>
                    <td>
                        @if($sub->status === 'accepted')
                            <span class="ed-badge ed-badge-green"><i class="fa-solid fa-circle-check me-1"></i> Accepted</span>
                        @elseif($sub->status === 'wrong_answer')
                            <span class="ed-badge ed-badge-red"><i class="fa-solid fa-circle-xmark me-1"></i> Wrong Answer</span>
                        @else
                            <span class="ed-badge ed-badge-yellow"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ strtoupper($sub->status) }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" class="ed-btn btn-sm" style="background:var(--brand);color:#fff;border:0;padding:5px 12px;font-size:12.5px;"
                                data-bs-toggle="modal" data-bs-target="#codeModal"
                                data-student-name="{{ $sub->user->name }}"
                                data-lang-name="{{ $sub->language->name ?? '' }}"
                                data-source-code="{{ $sub->code }}">
                            {{ __('To see') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📥</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('There are no solutions') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('So far, no student has sent an answer to this question') }}.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CODE MODAL --}}
<div class="modal fade ed-modal" id="codeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--card-border);color:var(--text);">
            <div class="modal-header" style="border-bottom:1px solid var(--card-border);">
                <div>
                    <h5 class="modal-title" style="font-weight:800;color:var(--text);"><i class="fa-solid fa-code me-2" style="color:var(--brand);"></i>{{ __('View the code') }}</h5>
                    <div style="font-size:12px;color:var(--text-muted);" id="codeModalSubtitle">{{ __('A student') }}: ... | {{ __('Language') }}: ...</div>
                </div>
                <button type="button" class="btn-close" aria-label="Close" style="filter:invert(1);" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="codeBlock" style="background:#0d0d0d;color:#f8f8f2;padding:16px;border-radius:var(--radius-lg);margin:0;font-size:13px;max-height:480px;overflow-y:auto;font-family:'Courier New', monospace;line-height:1.5;"></pre>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--card-border);">
                <button type="button" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);" data-bs-dismiss="modal">{{ __('Close out') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const codeModal = document.getElementById('codeModal');
codeModal?.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    const studentName = btn.dataset.studentName;
    const langName = btn.dataset.langName;
    const sourceCode = btn.dataset.sourceCode;

    document.getElementById('codeModalSubtitle').textContent = `{{ __('Student') }}: ${studentName} | {{ __('Language') }}: ${langName}`;
    document.getElementById('codeBlock').textContent = sourceCode;
});
</script>
@endsection