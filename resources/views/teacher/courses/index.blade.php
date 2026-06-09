@extends('teacher.layouts.app')
@section('title', 'My Courses')
@section('breadcrumb', 'My Courses')

@section('page-actions')
<a href="{{ route('teacher.courses.create') }}" class="ed-btn ed-btn-primary">
    <i class="fa-solid fa-plus"></i> Add New Course
</a>
@endsection

@section('content')

{{-- ── STATS ROW ───────────────────────────────────────────── --}}
<div class="row g-4 mb-4">
    @php
        $total     = $courses->total();
        $published = $courses->getCollection()->where('status','published')->count();
        $draft     = $courses->getCollection()->where('status','draft')->count();
    @endphp
    <div class="col-6 col-lg-3">
        <div class="ed-card" style="padding:20px 22px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">Total Courses</div>
            <div style="font-size:28px;font-weight:800;color:var(--text);margin:4px 0;">{{ $total }}</div>
            <span class="ed-badge ed-badge-indigo" style="font-size:11px;">All time</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ed-card" style="padding:20px 22px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">Published</div>
            <div style="font-size:28px;font-weight:800;color:#065f46;margin:4px 0;">{{ $courses->getCollection()->where('status','published')->count() }}</div>
            <span class="ed-badge ed-badge-green" style="font-size:11px;">Live</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ed-card" style="padding:20px 22px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">Drafts</div>
            <div style="font-size:28px;font-weight:800;color:var(--text-muted);margin:4px 0;">{{ $courses->getCollection()->where('status','draft')->count() }}</div>
            <span class="ed-badge ed-badge-yellow" style="font-size:11px;">Unpublished</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="ed-card" style="padding:20px 22px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);">Total Lessons</div>
            <div style="font-size:28px;font-weight:800;color:var(--text);margin:4px 0;">{{ $courses->getCollection()->sum('lessons_count') }}</div>
            <span class="ed-badge ed-badge-indigo" style="font-size:11px;">Across all courses</span>
        </div>
    </div>
</div>

{{-- ── COURSE TABLE CARD ───────────────────────────────────── --}}
<div class="ed-card">
    {{-- Card header --}}
    <div class="ed-card-header">
        <div>
            <div class="ed-card-title"><i class="fa-solid fa-table-list me-2" style="color:var(--brand);"></i>Course List</div>
            <div class="ed-card-subtitle">{{ $courses->total() }} course{{ $courses->total() !== 1 ? 's' : '' }} found</div>
        </div>
        {{-- Table search filter --}}
        <div style="position:relative;max-width:220px;flex:1;">
            <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:12px;"></i>
            <input type="search" id="tableFilter" placeholder="Filter courses…"
                   style="width:100%;border:1.5px solid rgba(99, 102, 241,.15);border-radius:50px;padding:8px 14px 8px 34px;font-size:13px;outline:none;background:#fafafe;color:var(--text);">
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table ed-table" id="courseTable">
            <thead>
                <tr>
                    <th style="width:52px;">#</th>
                    <th style="min-width:90px;">Thumbnail</th>
                    <th>Course Title</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Lessons</th>
                    <th class="text-end" style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody id="courseTableBody">
            @forelse($courses as $course)
                <tr>
                    {{-- Row number --}}
                    <td style="color:var(--text-muted);font-weight:600;font-size:13px;">
                        {{ ($courses->currentPage() - 1) * $courses->perPage() + $loop->iteration }}
                    </td>

                    {{-- Thumbnail --}}
                    <td>
                        <div class="ed-thumb">
                            @if($course->image_path)
                                <img src="{{ asset('storage/' . $course->image_path) }}" alt="{{ $course->title }}">
                            @else
                                <i class="fa-solid fa-photo-film"></i>
                            @endif
                        </div>
                    </td>

                    {{-- Title + description --}}
                    <td>
                        <div style="font-weight:700;color:var(--text);font-size:13.5px;line-height:1.3;">
                            {{ $course->title }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:3px;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ \Illuminate\Support\Str::limit($course->description, 65) }}
                        </div>
                    </td>

                    {{-- Category --}}
                    <td>
                        <span class="ed-badge ed-badge-indigo">
                            {{ $course->category->name ?? '—' }}
                        </span>
                    </td>

                    {{-- Price --}}
                    <td>
                        <span style="font-weight:800;font-size:14px;color:var(--text);">
                            ${{ number_format((float) $course->price, 2) }}
                        </span>
                    </td>

                    {{-- Status badge --}}
                    <td>
                        @if($course->status === 'published')
                            <span class="ed-badge ed-badge-green">Published</span>
                        @else
                            <span class="ed-badge ed-badge-yellow">Draft</span>
                        @endif
                    </td>

                    {{-- Lesson count --}}
                    <td>
                        <span style="font-size:13px;font-weight:600;color:var(--text-muted);">
                            <i class="fa-solid fa-play-circle me-1" style="color:var(--brand);"></i>
                            {{ $course->lessons_count ?? 0 }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            {{-- View --}}
                            <a href="{{ route('course.show', $course->slug) }}"
                               class="ed-action-btn ed-action-view"
                               title="View Course" target="_blank">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('teacher.courses.edit', $course) }}"
                               class="ed-action-btn ed-action-edit"
                               title="Edit Course">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            {{-- Modules Settings --}}
                            <a href="{{ route('teacher.courses.modules.index', $course) }}"
                               class="ed-action-btn ed-action-modules"
                               title="Modules Settings" style="background: rgba(99, 102, 241, 0.1); color: var(--brand);">
                                <i class="fa-solid fa-cubes"></i>
                            </a>

                            {{-- Delete trigger (Bootstrap modal) --}}
                            <button type="button"
                                    class="ed-action-btn ed-action-delete"
                                    title="Delete Course"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-course-id="{{ $course->id }}"
                                    data-course-title="{{ $course->title }}"
                                    data-delete-url="{{ route('teacher.courses.destroy', $course) }}">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📚</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">No courses yet</div>
                        <div style="color:var(--text-muted);font-size:13px;margin:6px 0 18px;">Create your first course to get started.</div>
                        <a href="{{ route('teacher.courses.create') }}" class="ed-btn ed-btn-primary">
                            <i class="fa-solid fa-plus"></i> Add First Course
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($courses->hasPages())
    <div class="ed-card-body border-top" style="padding:16px 24px;">
        {{ $courses->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     DELETE CONFIRMATION MODAL (Bootstrap 5)
══════════════════════════════════════════════════════════ --}}
<div class="modal fade ed-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--red);">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:800;color:var(--text);" id="deleteModalLabel">Delete Course</div>
                        <div style="font-size:12.5px;color:var(--text-muted);">This action cannot be undone</div>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px;color:var(--text);line-height:1.6;">
                    Are you sure you want to permanently delete the course:
                    <br>
                    <strong id="deleteCourseTitle" style="color:var(--red);">…</strong>
                </p>
                <div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);border-radius:10px;padding:12px 14px;font-size:12.5px;color:#7f1d1d;line-height:1.5;margin-top:10px;">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    All lessons, enrollments, and media associated with this course will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="ed-btn ed-btn-outline" data-bs-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ed-btn ed-btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Yes, Delete Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// ── Delete modal wiring ──────────────────────────────────────
const deleteModal = document.getElementById('deleteModal');
deleteModal?.addEventListener('show.bs.modal', (event) => {
    const btn   = event.relatedTarget;
    const title = btn.dataset.courseTitle;
    const url   = btn.dataset.deleteUrl;

    document.getElementById('deleteCourseTitle').textContent = title;
    document.getElementById('deleteForm').action = url;
});

// ── Client-side table filter ─────────────────────────────────
document.getElementById('tableFilter')?.addEventListener('input', function () {
    const q    = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#courseTableBody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endsection
