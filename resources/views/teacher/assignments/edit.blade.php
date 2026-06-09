@extends('teacher.layouts.app')
@section('title', __('Change Task'))
@section('breadcrumb', __('Change Task'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-pen-to-square me-2" style="color:var(--brand);"></i>{{ __('Editing an assignment') }}</div>
                    <div class="ed-card-subtitle">{{ __('Task content') }}, {{ __('Change the score and deadlines') }}</div>
                </div>
                <a href="{{ route('teacher.assignments.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.assignments.update', $assignment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Well') }} <span class="text-danger">*</span></label>
                            <select name="course_id" id="courseSelect" class="form-select @error('course_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $assignment->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Module') }} ({{ __('Department') }})</label>
                            <select name="module_id" id="moduleSelect" class="form-select @error('module_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                                <option value="">{{ __('Select a module') }} ({{ __('if you want') }})...</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" data-course-id="{{ $module->course_id }}" {{ old('module_id', $assignment->module_id) == $module->id ? 'selected' : '' }} style="display:none;">
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
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $assignment->title) }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Explanation of the task') }} <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('description', $assignment->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Implementation guidelines') }} (Instructions)</label>
                        <textarea name="instructions" rows="4" class="form-control @error('instructions') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('instructions', $assignment->instructions) }}</textarea>
                        @error('instructions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Deadline') }}</label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Maximum score') }} <span class="text-danger">*</span></label>
                            <input type="number" name="max_score" class="form-control @error('max_score') is-invalid @enderror" value="{{ old('max_score', $assignment->max_score) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('max_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Order number') }} <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $assignment->sort_order) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Condition') }} <span class="text-danger">*</span></label>
                            <select name="is_published" class="form-select @error('is_published') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                                <option value="1" {{ old('is_published', $assignment->is_published) ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="0" {{ !old('is_published', $assignment->is_published) ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            </select>
                            @error('is_published')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.assignments.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                            {{ __('Cancellation') }}
                        </a>
                        <button type="submit" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
                            <i class="fa-solid fa-save"></i> {{ __('Save changes') }}
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

    function filterModules(selectedVal = null) {
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
        
        if (selectedVal) {
            moduleSelect.value = selectedVal;
        } else {
            moduleSelect.value = '';
        }
    }

    courseSelect.addEventListener('change', () => filterModules());
    
    // Run initially
    if (courseSelect.value) {
        filterModules("{{ old('module_id', $assignment->module_id) }}");
    }
});
</script>
@endsection