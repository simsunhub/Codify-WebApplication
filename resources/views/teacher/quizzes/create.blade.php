@extends('teacher.layouts.app')
@section('title', __('Create a New Test'))
@section('breadcrumb', __('New Test'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new test') }}</div>
                    <div class="ed-card-subtitle">{{ __('Choose a course') }}, {{ __('specify the test settings') }}</div>
                </div>
                <a href="{{ route('teacher.quizzes.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.quizzes.store') }}" method="POST">
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
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Name of the test') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('For example: Mid-term examination on Module 1') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Explanation of the test') }}</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('A summary of how many questions are in the test and its purpose...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('It\'s time') }} ({{ __('in minutes') }})</label>
                            <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes') }}" min="1" placeholder="{{ __('Leave blank if unlimited') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Pass limit') }} (%) <span class="text-danger">*</span></label>
                            <input type="number" name="pass_percentage" class="form-control @error('pass_percentage') is-invalid @enderror" value="{{ old('pass_percentage', 60) }}" min="1" max="100" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('pass_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Number of attempts') }} <span class="text-danger">*</span></label>
                            <input type="number" name="max_attempts" class="form-control @error('max_attempts') is-invalid @enderror" value="{{ old('max_attempts', 3) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('max_attempts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Order number') }} <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 1) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Condition') }} <span class="text-danger">*</span></label>
                        <select name="is_published" class="form-select @error('is_published') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            <option value="1" {{ old('is_published', '1') == '1' ? 'selected' : '' }}>{{ __('Published') }}</option>
                            <option value="0" {{ old('is_published', '1') == '0' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                        </select>
                        @error('is_published')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.quizzes.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
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
    
    // Initial run
    if (courseSelect.value) {
        filterModules();
        @if(old('module_id'))
            moduleSelect.value = "{{ old('module_id') }}";
        @endif
    }
});
</script>
@endsection