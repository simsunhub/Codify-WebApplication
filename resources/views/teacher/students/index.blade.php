@extends('teacher.layouts.app')
@section('title', __('List of students'))
@section('breadcrumb', __('Students'))

@section('content')

<div class="ed-card">
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-users me-2" style="color:var(--brand);"></i>{{ __('Enrolled students') }}</div>
            <div class="ed-card-subtitle">{{ __('A list of students studying in your courses') }}</div>
        </div>
        <div style="position:relative;max-width:240px;flex:1;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:12px;"></i>
            <input type="search" id="tableFilter" placeholder="{{ __('Look for…') }}"
                   style="width:100%;border:1.5px solid var(--card-border);border-radius:50px;padding:8px 14px 8px 34px;font-size:13px;outline:none;background:var(--card-bg2);color:var(--text);">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table ed-table" id="studentsTable">
            <thead>
                <tr>
                    <th style="width:60px;">#</th>
                    <th>{{ __('Name of the student') }}-{{ __('about') }}</th>
                    <th>{{ __('Email address') }} (Email)</th>
                    <th>{{ __('Number of registered courses') }}</th>
                    <th class="text-end" style="width:120px;">{{ __('To see') }}</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">
            @forelse($students as $student)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $student->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ __('Date of registration') }}: {{ $student->created_at->format('d.m.Y') }}</div>
                    </td>
                    <td>
                        <span style="font-size:13.5px;color:var(--text);">{{ $student->email }}</span>
                    </td>
                    <td>
                        <span class="ed-badge ed-badge-indigo" style="font-weight:700;">
                            {{ $student->enrollments_count }} {{ __('well') }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('teacher.students.show', $student->id) }}"
                           class="ed-btn btn-sm" style="background:rgba(99, 102, 241,.1);color:var(--brand);border:0;padding:5px 12px;font-size:12.5px;text-decoration:none;"
                           title="{{ __('Student profile and achievements') }}">
                            <i class="fa-solid fa-eye me-1"></i> {{ __('Full view') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">👥</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('There are no students') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;">{{ __('No students have registered for your courses yet') }}.</div>
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
    const rows = document.querySelectorAll('#studentsTableBody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection