@extends('teacher.layouts.app')
@section('title', __('Add New Task'))
@section('breadcrumb', __('New Task'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new task') }}</div>
                    <div class="ed-card-subtitle">{{ __('Choose a course') }}, {{ __('specify task content and deadlines') }}</div>
                </div>
                <a href="{{ route('teacher.assignments.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.assignments.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Well') }} <span class="text-danger">*</span></label>
                            <select name="course_id" id="courseSelect" class="form-select @error('course_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                                <option value="">{{ __('Select a course') }}...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Module') }} ({{ __('Department') }})</label>
                            <select name="module_id" id="moduleSelect" class="form-select @error('module_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                                <option value="">{{ __('Once a course is selected, the modules will open') }}...</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" data-course-id="{{ $module->course_id }}" {{ old('module_id') == $module->id ? 'selected' : '' }} style="display:none;">
                                        {{ $module->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('module_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('The title of the task') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('For example: Practice: Using arrays') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Explanation of the task') }} <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('Write information about what the assignment is about and the purpose...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Implementation guidelines') }} (Instructions)</label>
                        <textarea name="instructions" rows="4" class="form-control @error('instructions') is-invalid @enderror" placeholder="{{ __('Instructions on how to complete and submit an assignment...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('instructions') }}</textarea>
                        @error('instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Deadline') }} (Due Date)</label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Maximum score') }} <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" class="form-control @error('max_score') is-invalid @enderror" value="{{ old('max_score', 100) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('max_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Order number') }} <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 1) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.assignments.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                            {{ __('Cancellation') }}
                        </a>
                        <button type="submit" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
                            <i class="fa-solid fa-save"></i> {{ __('To keep') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('courseSelect');
    const moduleSelect = document.getElementById('moduleSelect');

    function filterModules() {
        const courseId = courseSelect.value;
        if (!courseId) {
            moduleSelect.value = '';
            Array.from(moduleSelect.options).forEach(opt => {
                if (opt.value) opt.style.display = 'none';
            });
            moduleSelect.options[0].textContent = '{{ __('First choose a course...') }}';
            return;
        }

        let hasModules = false;
        Array.from(moduleSelect.options).forEach(opt => {
            if (!opt.value) return;
            if (opt.dataset.courseId == courseId) {
                opt.style.display = '';
                hasModules = true;
            } else {
                opt.style.display = 'none';
            }
        });

        if (hasModules) {
            moduleSelect.options[0].textContent = '{{ __('Select a module (if desired)...') }}';
        } else {
            moduleSelect.options[0].textContent = '{{ __('There are no modules in this course...') }}';
        }
        moduleSelect.value = '';
    }

    courseSelect.addEventListener('change', filterModules);
    
    // Initial run in case of validation errors with old values
    if (courseSelect.value) {
        filterModules();
        @if(old('module_id'))
            moduleSelect.value = "{{ old('module_id') }}";
        @endif
    }
});
</script>
@endsection