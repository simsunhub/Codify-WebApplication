@extends('teacher.layouts.app')
@section('title', __('Task Management'))
@section('breadcrumb', __('Tasks'))

@section('page-actions')
<a href="{{ route('teacher.assignments.create') }}" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
    <i class="fa-solid fa-plus"></i> {{ __('Add a new task') }}
</a>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(16,185,129,.1);color:var(--green);border:1px solid rgba(16,185,129,.2);border-radius:var(--radius-md);">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);"></button>
</div>
@endif

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-tasks me-2" style="color:var(--brand);"></i>{{ __('Task list') }}</div>
            <div class="ed-card-subtitle">{{ __('In general') }} {{ $assignments->count() }} {{ __('task found') }}</div>
        </div>
        <div style="position:relative;max-width:240px;flex:1;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:12px;"></i>
            <input type="search" id="tableFilter" placeholder="{{ __('Look for…') }}"
                   style="width:100%;border:1.5px solid var(--card-border);border-radius:50px;padding:8px 14px 8px 34px;font-size:13px;outline:none;background:var(--card-bg2);color:var(--text);">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table" id="assignmentsTable">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('Task') }}</th>
                    <th>{{ __('Course/Module') }}</th>
                    <th>{{ __('Maximum score') }}</th>
                    <th>{{ __('Shipments') }}</th>
                    <th>{{ __('Condition') }}</th>
                    <th class="text-end" style="width:180px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="assignmentsTableBody">
            @forelse($assignments as $assignment)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $assignment->title }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                            {{ __('Deadline') }}: {{ $assignment->due_date ? $assignment->due_date->format('d.m.Y H:i') : __('Without a deadline') }}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--text-muted);font-size:13px;">{{ $assignment->course->title ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--brand);margin-top:2px;">{{ $assignment->module->title ?? __('Not attached to the department') }}</div>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="font-weight:700;font-size:12px;">
                            {{ $assignment->max_score }} {{ __('score') }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('teacher.assignments.submissions', $assignment->id) }}" class="ed-badge ed-badge-indigo" style="background:rgba(99, 102, 241,.1);color:var(--brand);text-decoration:none;">
                            <i class="fa-solid fa-paper-plane me-1"></i>
                            {{ $assignment->submissions()->count() }} {{ __('send off') }}
                        </a>
                    </td>
                    <td>
                        @if($assignment->is_published)
                            <span class="ed-badge ed-badge-green">{{ __('Published') }}</span>
                        @else
                            <span class="ed-badge ed-badge-yellow">{{ __('Draft') }}</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('teacher.assignments.submissions', $assignment->id) }}"
                               class="ed-action-btn" style="color:var(--brand);background:rgba(99, 102, 241,.1);padding:5px 8px;border-radius:var(--radius-sm);font-size:12px;text-decoration:none;"
                               title="{{ __('Shipments') }}">
                                <i class="fa-solid fa-inbox"></i>
                            </a>

                            <a href="{{ route('teacher.assignments.edit', $assignment) }}"
                               class="ed-action-btn ed-action-edit" style="color:#3b82f6;background:rgba(59,130,246,.1);"
                               title="{{ __('Change') }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <button type="button"
                                    class="ed-action-btn ed-action-delete" style="color:var(--red);background:rgba(239,68,68,.1);border:0;"
                                    title="{{ __('Power off') }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-assignment-title="{{ $assignment->title }}"
                                    data-delete-url="{{ route('teacher.assignments.destroy', $assignment) }}">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📝</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('There are no assignments') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;margin:6px 0 18px;">{{ __('Add a new task to test your students\' knowledge') }}.</div>
                        <a href="{{ route('teacher.assignments.create') }}" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
                            <i class="fa-solid fa-plus"></i> {{ __('Add the first task') }}
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div class="modal fade ed-modal" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--card-bg);border:1px solid var(--card-border);color:var(--text);">
            <div class="modal-header" style="border-bottom:1px solid var(--card-border);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--red);">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:var(--text);">{{ __('Disable task') }}</div>
                        <div style="font-size:12.5px;color:var(--text-muted);">{{ __('This action cannot be undone') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px;color:var(--text);line-height:1.6;">
                    {{ __('Are you sure you want to permanently delete this task?') }}:
                    <br>
                    <strong id="deleteAssignmentTitle" style="color:var(--red);">…</strong>
                </p>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--card-border);">
                <button type="button" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);" data-bs-dismiss="modal">
                    {{ __('Cancellation') }}
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ed-btn" style="background:var(--red);color:#fff;border:0;">
                        {{ __('Oh yes') }}, {{ __('Power off') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const deleteModal = document.getElementById('deleteModal');
deleteModal?.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    const title = btn.dataset.assignmentTitle;
    const url = btn.dataset.deleteUrl;

    document.getElementById('deleteAssignmentTitle').textContent = title;
    document.getElementById('deleteForm').action = url;
});

document.getElementById('tableFilter')?.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#assignmentsTableBody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection