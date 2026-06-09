@extends('admin.layouts.app')

@section('title', __('Course Changes'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">{{ __('Course name') }} *</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', $course->title) }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Category') }} *</label>
                            <select name="category_id" class="form-select">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $course->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Price') }} ($) *</label>
                            <input type="number" name="price" class="form-control"
                                   value="{{ old('price', $course->price) }}" min="0" step="0.01">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Level') }} *</label>
                            <select name="level" class="form-select">
                                <option value="beginner" {{ $course->level == 'beginner' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                                <option value="intermediate" {{ $course->level == 'intermediate' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                <option value="advanced" {{ $course->level == 'advanced' ? 'selected' : '' }}>{{ __('High') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Photo') }}</label>
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" class="d-block mb-2 rounded" height="60">
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ $course->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">{{ __('Active') }}</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Update') }}
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection