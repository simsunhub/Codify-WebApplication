@extends('admin.layouts.app')

@section('title', __('messages.admin.categories.edit'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="edudash-card border-0 shadow-sm" style="border-radius: var(--radius-xl); background: #ffffff;">
            <div class="edudash-card-header bg-white py-3" style="border-bottom: 1px solid var(--border);">
                <h5 class="edudash-card-title mb-0 text-dark fw-bold d-flex align-items-center">
                    <i class="fa-solid fa-edit me-2 text-warning"></i>
                    {{ __('messages.admin.categories.edit') }}
                </h5>
            </div>
            <div class="edudash-card-body p-4">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryName">
                                {{ __('messages.admin.categories.name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="categoryName" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $category->name) }}" required
                                   style="border-radius: var(--radius-md);">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categorySlug">
                                {{ __('messages.admin.categories.slug') }}
                            </label>
                            <input type="text" name="slug" id="categorySlug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $category->slug) }}"
                                   style="border-radius: var(--radius-md);">
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Icon Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryIcon">
                                {{ __('messages.admin.categories.icon') }} (FontAwesome)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: var(--radius-md) 0 0 var(--radius-md); border-color: rgba(15, 23, 42, 0.12);">
                                    <i id="iconPreview" class="{{ !str_starts_with(trim($category->icon), '<svg') ? ($category->icon ?? 'fas fa-laptop-code') : 'fas fa-laptop-code' }} text-primary" style="color: var(--brand) !important;"></i>
                                </span>
                                <input type="text" name="icon" id="categoryIcon" class="form-control border-start-0 @error('icon') is-invalid @enderror"
                                       value="{{ old('icon', !str_starts_with(trim($category->icon), '<svg') ? $category->icon : 'fas fa-laptop-code') }}"
                                       style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                            </div>
                            @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Photo/Image (optional) -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryImage">
                                {{ __('Photo') }}
                            </label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($category->image) }}" class="rounded border bg-light p-1" height="60" alt="Category Image">
                                </div>
                            @endif
                            <input type="file" name="image" id="categoryImage" class="form-control @error('image') is-invalid @enderror" accept="image/*"
                                   style="border-radius: var(--radius-md);">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- SVG Code Input -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="svgCode">
                                SVG İkon Kodu (FontAwesome yerine geçer)
                            </label>
                            <textarea name="svg_code" id="svgCode" class="form-control @error('svg_code') is-invalid @enderror" 
                                      rows="1" placeholder="<svg ...>...</svg>"
                                      style="border-radius: var(--radius-md); resize: vertical; font-family: monospace; font-size: 13px;">{{ old('svg_code', str_starts_with(trim($category->icon), '<svg') ? $category->icon : '') }}</textarea>
                            @error('svg_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- SVG File Input -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="svgFile">
                                SVG İkon Dosyası Yükle (.svg)
                            </label>
                            <input type="file" name="svg_file" id="svgFile" accept=".svg" class="form-control @error('svg_code') is-invalid @enderror" 
                                   style="border-radius: var(--radius-md);">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryDesc">
                                {{ __('messages.dash.content_field') }}
                            </label>
                            <textarea name="description" id="categoryDesc" class="form-control @error('description') is-invalid @enderror"
                                      rows="3" style="border-radius: var(--radius-md);">{{ old('description', $category->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Active status -->
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="categoryActive" value="1"
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-medium" for="categoryActive">
                                    {{ __('messages.admin.categories.active') }}
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light rounded-xl px-4 py-2 border font-semibold">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('messages.dash.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary rounded-xl px-4 py-2 text-white shadow-sm font-semibold transition-all hover:opacity-90" style="background-color: var(--brand); border-color: var(--brand);">
                                <i class="fas fa-save me-2"></i>{{ __('messages.dash.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('categoryIcon');
        const iconPreview = document.getElementById('iconPreview');

        if (iconInput && iconPreview) {
            iconInput.addEventListener('input', function() {
                iconPreview.className = iconInput.value || 'fas fa-laptop-code';
            });
        }
    });
</script>
@endpush