@extends('teacher.layouts.app')
@section('title', __('Assignment Submissions'))
@section('breadcrumb', __('Assignment Submissions'))

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(16,185,129,.1);color:var(--green);border:1px solid rgba(16,185,129,.2);border-radius:var(--radius-md);">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);"></button>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="ed-card" style="border-left:4px solid var(--brand);">
            <div class="ed-card-body" style="padding:22px 26px;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">{{ __('The title of the task') }}</div>
                        <h4 style="font-weight:800;color:var(--text);margin:6px 0 4px;">{{ $assignment->title }}</h4>
                        <div style="font-size:13px;color:var(--text-muted);">
                            {{ __('Well') }}: <strong>{{ $assignment->course->title ?? '—' }}</strong> | 
                            {{ __('Maximum score') }}: <span class="badge bg-dark">{{ $assignment->max_score }}</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.assignments.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('Return to tasks') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-inbox me-2" style="color:var(--brand);"></i>{{ __('Student Dispatches') }}</div>
            <div class="ed-card-subtitle">{{ __('In general') }} {{ $submissions->count() }} {{ __('The student sent the answer') }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('A student') }}</th>
                    <th>{{ __('Time sent') }}</th>
                    <th>{{ __('The answer is file/text') }}</th>
                    <th>{{ __('Condition') }}</th>
                    <th>{{ __('Score') }}</th>
                    <th class="text-end" style="width:120px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($submissions as $submission)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $submission->user->name ?? 'Student' }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $submission->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div style="font-size:13.5px;color:var(--text);">{{ $submission->created_at->format('d.m.Y H:i') }}</div>
                    </td>
                    <td>
                        @if($submission->file_path)
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="ed-badge ed-badge-indigo" style="background:rgba(99, 102, 241,.1);color:#6366f1;text-decoration:none;">
                                <i class="fa-solid fa-file-arrow-down me-1"></i> {{ __('Upload a file') }}
                            </a>
                        @endif
                        @if($submission->submission_text)
                            <div style="font-size:12px;color:var(--text-muted);max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:4px;" title="{{ $submission->submission_text }}">
                                {{ $submission->submission_text }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($submission->status === 'graded')
                            <span class="ed-badge ed-badge-green">{{ __('Evaluated') }}</span>
                        @else
                            <span class="ed-badge ed-badge-yellow">{{ __('Unverified') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($submission->status === 'graded')
                            <strong style="color:var(--green);font-size:14px;">{{ $submission->score }}</strong> / {{ $assignment->max_score }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" class="ed-btn btn-sm" style="background:var(--brand);color:#fff;border:0;padding:5px 12px;font-size:12.5px;"
                                data-bs-toggle="modal" data-bs-target="#gradeModal"
                                data-submission-id="{{ $submission->id }}"
                                data-student-name="{{ $submission->user->name }}"
                                data-submission-text="{{ $submission->submission_text }}"
                                data-file-url="{{ $submission->file_path ? asset('storage/' . $submission->file_path) : '' }}"
                                data-score="{{ $submission->score ?? '' }}"
                                data-feedback="{{ $submission->feedback ?? '' }}">
                            {{ $submission->status === 'graded' ? __('Re-evaluation') : __('To assess') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📥</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('No shipments') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('No student has yet submitted an answer to this assignment') }}.</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- GRADING MODAL --}}
<div class="modal fade ed-modal" id="gradeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--card-border);color:var(--text);">
            <div class="modal-header" style="border-bottom:1px solid var(--card-border);">
                <h5 class="modal-title" style="font-weight:800;"><i class="fa-solid fa-graduation-cap me-2" style="color:var(--brand);"></i>{{ __('Task evaluation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);" data-bs-dismiss="modal"></button>
            </div>
            <form id="gradeForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text-muted);">{{ __('A student') }}:</label>
                        <div id="modalStudentName" style="font-weight:700;font-size:16px;color:var(--text);">...</div>
                    </div>

                    <div class="mb-3" id="modalFileContainer" style="display:none;">
                        <label class="form-label" style="font-weight:600;color:var(--text-muted);">{{ __('Attached file') }}:</label>
                        <div>
                            <a id="modalFileUrl" href="" target="_blank" class="btn btn-outline-light btn-sm">
                                <i class="fa-solid fa-file-arrow-down me-1"></i> {{ __('View / download file') }}
                            </a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text-muted);">{{ __('Student\'s written response') }}:</label>
                        <div id="modalSubmissionText" style="background:var(--card-bg2);border:1px solid var(--card-border);border-radius:var(--radius-md);padding:14px;font-size:13.5px;color:var(--text);white-space:pre-wrap;max-height:220px;overflow-y:auto;line-height:1.6;">
                            —
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Score') }} <span class="text-danger">*</span> ({{ __('Max') }}. {{ $assignment->max_score }})</label>
                            <input type="number" name="score" id="modalScoreInput" class="form-control" min="0" max="{{ $assignment->max_score }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Teacher\'s opinion') }} (Feedback)</label>
                        <textarea name="feedback" id="modalFeedbackInput" rows="4" class="form-control" placeholder="{{ __('Give feedback to the student about their mistakes and achievements...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--card-border);">
                    <button type="button" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);" data-bs-dismiss="modal">{{ __('Return') }}</button>
                    <button type="submit" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">{{ __('Price maintenance') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const gradeModal = document.getElementById('gradeModal');
gradeModal?.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    const subId = btn.dataset.submissionId;
    const studentName = btn.dataset.studentName;
    const subText = btn.dataset.submissionText;
    const fileUrl = btn.dataset.fileUrl;
    const score = btn.dataset.score;
    const feedback = btn.dataset.feedback;

    // Action url
    const form = document.getElementById('gradeForm');
    form.action = `/teacher/assignments/submission/${subId}/grade`;

    // Populate data
    document.getElementById('modalStudentName').textContent = studentName;
    
    // File
    const fileContainer = document.getElementById('modalFileContainer');
    const fileLink = document.getElementById('modalFileUrl');
    if (fileUrl) {
        fileLink.href = fileUrl;
        fileContainer.style.display = 'block';
    } else {
        fileContainer.style.display = 'none';
    }

    // Text submission
    document.getElementById('modalSubmissionText').textContent = subText || '{{ __('No text added.') }}';

    // Score and feedback inputs
    document.getElementById('modalScoreInput').value = score || '';
    document.getElementById('modalFeedbackInput').value = feedback || '';
});
</script>
@endsection