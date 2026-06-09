@extends('admin.layouts.app')

@section('title', __('Kos course'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">{{ __('Course name') }} *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}" placeholder="{{ __('Ex: Laravel 11 full course') }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Category') }} *</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">{{ __('Choose') }}...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="{{ __('Details about the course...') }}">{{ old('description') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Price') }} ($) *</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', 0) }}" min="0" step="0.01">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Level') }} *</label>
                            <select name="level" class="form-select">
                                <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                                <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>{{ __('High') }}</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">{{ __('Photo') }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">{{ __('Active') }}</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Save') }}
                        </button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection