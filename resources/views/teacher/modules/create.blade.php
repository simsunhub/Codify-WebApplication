@extends('teacher.layouts.app')
@section('title', __('Create a New Module'))
@section('breadcrumb', __('New Module'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new module') }}</div>
                    <div class="ed-card-subtitle">{{ __('Choose the appropriate course') }}, {{ __('Fill in the section details') }}</div>
                </div>
                <a href="{{ route('teacher.modules.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.modules.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Well') }} <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            <option value="">{{ __('Select a course') }}...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Module name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('For example: Chapter 1: Introduction and Fundamentals') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Brief explanation') }}</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('About the main purpose of the module and the topics covered...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Order number') }} ({{ __('Sorting') }}) <span class="text-danger">*</span></label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 1) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        <div class="form-text" style="color:var(--text-muted);">{{ __('Used to determine the sequence of modules in a course') }}.</div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.modules.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
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