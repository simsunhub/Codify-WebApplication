@extends('teacher.layouts.app')
@section('title', __('Change Module'))
@section('breadcrumb', __('Change Module'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-pen-to-square me-2" style="color:var(--brand);"></i>{{ __('Repair the module') }}</div>
                    <div class="ed-card-subtitle">{{ __('Change the module\'s basic data and publication status') }}</div>
                </div>
                <a href="{{ route('teacher.modules.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.modules.update', $module) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Well') }} <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', $module->course_id) == $course->id) ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Module name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $module->title) }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Brief explanation') }}</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('description', $module->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Order number') }} <span class="text-danger">*</span></label>
                            <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $module->sort_order) }}" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Condition') }} <span class="text-danger">*</span></label>
                            <select name="is_published" class="form-select @error('is_published') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                                <option value="1" {{ old('is_published', $module->is_published) ? 'selected' : '' }}>{{ __('Published') }}</option>
                                <option value="0" {{ !old('is_published', $module->is_published) ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            </select>
                            @error('is_published')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.modules.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
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