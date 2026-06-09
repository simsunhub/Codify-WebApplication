@extends('teacher.layouts.app')

@section('title', __('messages.admin.modules.course_override_title'))
@section('breadcrumb', __('messages.admin.modules.course_override_title'))

@section('content')
<div class="mb-4">
    <a href="{{ route('teacher.courses.index') }}" class="ed-btn ed-btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Course List
    </a>
</div>

<div class="row g-4">
    {{-- Left column: Toggle settings form --}}
    <div class="col-lg-7">
        <div class="ed-card">
            <div class="ed-card-header d-flex align-items-center justify-content-between">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-sliders me-2 text-primary"></i>{{ __('messages.admin.modules.course_override_title') }}
                    </div>
                    <div class="ed-card-subtitle">Course: <strong>{{ $course->title }}</strong></div>
                </div>
            </div>

            <div class="ed-card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success bg-success-subtle text-success border-0 px-4 py-3 rounded-3 mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                <form action="{{ route('teacher.courses.modules.update', $course->id) }}" method="POST">
                    @csrf
                    
                    {{-- Assignments toggle --}}
                    <div class="p-3 mb-3 border rounded-3 bg-dark-subtle d-flex align-items-center justify-content-between" style="border-color: rgba(255,255,255,.06) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color: var(--brand);">
                                <i class="fa-solid fa-tasks fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-white">{{ __('messages.dash.assignments') }}</h6>
                                <small class="text-muted">Allow students to upload course homework & tasks</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="modules[assignments]" value="0">
                            <input class="form-check-input ed-module-toggle" type="checkbox" 
                                   name="modules[assignments]" value="1"
                                   id="toggle-assignments"
                                   {{ $settings['assignments']->is_enabled ? 'checked' : '' }}
                                   style="width: 46px; height: 23px; cursor: pointer;">
                        </div>
                    </div>

                    {{-- Quizzes toggle --}}
                    <div class="p-3 mb-3 border rounded-3 bg-dark-subtle d-flex align-items-center justify-content-between" style="border-color: rgba(255,255,255,.06) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 rounded-3 bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color: var(--blue);">
                                <i class="fa-solid fa-question-circle fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-white">{{ __('messages.dash.quizzes') }}</h6>
                                <small class="text-muted">Allow students to take course tests & quizzes</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="modules[quizzes]" value="0">
                            <input class="form-check-input ed-module-toggle" type="checkbox" 
                                   name="modules[quizzes]" value="1"
                                   id="toggle-quizzes"
                                   {{ $settings['quizzes']->is_enabled ? 'checked' : '' }}
                                   style="width: 46px; height: 23px; cursor: pointer;">
                        </div>
                    </div>

                    {{-- Practice toggle --}}
                    <div class="p-3 mb-4 border rounded-3 bg-dark-subtle d-flex align-items-center justify-content-between" style="border-color: rgba(255,255,255,.06) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color: var(--green);">
                                <i class="fa-solid fa-code fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-white">{{ __('messages.dash.practice') }}</h6>
                                <small class="text-muted">Display coding exercises for this course</small>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="modules[practice]" value="0">
                            <input class="form-check-input ed-module-toggle" type="checkbox" 
                                   name="modules[practice]" value="1"
                                   id="toggle-practice"
                                   {{ $settings['practice']->is_enabled ? 'checked' : '' }}
                                   style="width: 46px; height: 23px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="ed-btn ed-btn-primary px-4 py-2.5">
                            <i class="fa-solid fa-save me-2"></i>{{ __('messages.dash.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Right column: Quick links to content editors --}}
    <div class="col-lg-5">
        <div class="ed-card">
            <div class="ed-card-header">
                <div class="ed-card-title">
                    <i class="fa-solid fa-pen-ruler me-2 text-warning"></i>Course Content Editors
                </div>
                <div class="ed-card-subtitle">Manage quizzes, tasks, and coding problems directly</div>
            </div>
            
            <div class="ed-card-body p-4 d-flex flex-column gap-3">
                {{-- Quiz link --}}
                <div class="p-3 border rounded-3 bg-dark-subtle d-flex flex-column gap-3" style="border-color: rgba(255,255,255,.06) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-4 text-info"><i class="fa-solid fa-file-signature"></i></div>
                        <div>
                            <h6 class="mb-0 text-white">Course Quizzes</h6>
                            <small class="text-muted">Create exams, select questions, and view results.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.quizzes.index') }}" class="ed-btn ed-btn-outline btn-sm w-100">
                            <i class="fa-solid fa-list me-1"></i>List Quizzes
                        </a>
                        <a href="{{ route('teacher.quizzes.create') }}" class="ed-btn ed-btn-primary btn-sm w-100">
                            <i class="fa-solid fa-plus me-1"></i>Add Quiz
                        </a>
                    </div>
                </div>

                {{-- Assignment link --}}
                <div class="p-3 border rounded-3 bg-dark-subtle d-flex flex-column gap-3" style="border-color: rgba(255,255,255,.06) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-4 text-primary"><i class="fa-solid fa-upload"></i></div>
                        <div>
                            <h6 class="mb-0 text-white">Course Assignments</h6>
                            <small class="text-muted">Add assignments for students to complete and grade submissions.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.assignments.index') }}" class="ed-btn ed-btn-outline btn-sm w-100">
                            <i class="fa-solid fa-list me-1"></i>List Assignments
                        </a>
                        <a href="{{ route('teacher.assignments.create') }}" class="ed-btn ed-btn-primary btn-sm w-100">
                            <i class="fa-solid fa-plus me-1"></i>Add Assignment
                        </a>
                    </div>
                </div>

                {{-- Coding link --}}
                <div class="p-3 border rounded-3 bg-dark-subtle d-flex flex-column gap-3" style="border-color: rgba(255,255,255,.06) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-4 text-success"><i class="fa-solid fa-code"></i></div>
                        <div>
                            <h6 class="mb-0 text-white">Coding Problems</h6>
                            <small class="text-muted">Create algorithmic problems with unit tests and test cases.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.coding.index') }}" class="ed-btn ed-btn-outline btn-sm w-100">
                            <i class="fa-solid fa-list me-1"></i>List Problems
                        </a>
                        <a href="{{ route('teacher.coding.create') }}" class="ed-btn ed-btn-primary btn-sm w-100">
                            <i class="fa-solid fa-plus me-1"></i>Add Problem
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-subtle {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    .form-switch .form-check-input:checked {
        background-color: var(--brand) !important;
        border-color: var(--brand) !important;
    }
    .form-switch .form-check-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(255,255,255,.25)'/%3e%3c/svg%3e") !important;
    }
    .form-switch .form-check-input:checked {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
    }
</style>
@endsection
