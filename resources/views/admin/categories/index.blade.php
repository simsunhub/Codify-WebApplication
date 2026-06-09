@extends('admin.layouts.app')

@section('title', __('messages.admin.categories.title'))

@section('content')
<!-- Create Category Form at the Top -->
<div class="edudash-card mb-4 border-0 shadow-sm" style="border-radius: var(--radius-xl); background: #ffffff;">
    <div class="edudash-card-header bg-white py-3" style="border-bottom: 1px solid var(--border);">
        <h5 class="edudash-card-title mb-0 text-dark fw-bold d-flex align-items-center">
            <i class="fa-solid fa-folder-plus me-2 text-primary" style="color: var(--brand) !important;"></i>
            {{ __('messages.admin.categories.add') }}
        </h5>
    </div>
    <div class="edudash-card-body p-4">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <!-- Name -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryName">
                        {{ __('messages.admin.categories.name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="categoryName" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" placeholder="{{ __('messages.admin.categories.placeholder_name') }}" required
                           style="border-radius: var(--radius-md);">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Slug -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categorySlug">
                        {{ __('messages.admin.categories.slug') }}
                    </label>
                    <input type="text" name="slug" id="categorySlug" class="form-control @error('slug') is-invalid @enderror" 
                           value="{{ old('slug') }}" placeholder="{{ __('messages.admin.categories.placeholder_slug') }}"
                           style="border-radius: var(--radius-md);">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Icon Selection -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryIcon">
                        {{ __('messages.admin.categories.icon') }} (FontAwesome)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: var(--radius-md) 0 0 var(--radius-md); border-color: rgba(15, 23, 42, 0.12);">
                            <i id="iconPreview" class="fas fa-laptop-code text-primary" style="color: var(--brand) !important;"></i>
                        </span>
                        <input type="text" name="icon" id="categoryIcon" class="form-control border-start-0 @error('icon') is-invalid @enderror" 
                               value="{{ old('icon', 'fas fa-laptop-code') }}" placeholder="{{ __('messages.admin.categories.placeholder_icon') }}"
                               style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                    </div>
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- SVG Code Input -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="svgCode">
                        SVG İkon Kodu (Önceliklidir)
                    </label>
                    <textarea name="svg_code" id="svgCode" class="form-control @error('svg_code') is-invalid @enderror" 
                              rows="1" placeholder="<svg ...>...</svg>"
                              style="border-radius: var(--radius-md); resize: none; font-family: monospace; font-size: 13px;">{{ old('svg_code') }}</textarea>
                    @error('svg_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- SVG File Input -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="svgFile">
                        SVG İkon Dosyası
                    </label>
                    <input type="file" name="svg_file" id="svgFile" accept=".svg" class="form-control @error('svg_code') is-invalid @enderror" 
                           style="border-radius: var(--radius-md);">
                </div>

                <!-- Description & Image Dropzone -->
                <div class="col-md-8">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2" for="categoryDesc">
                        {{ __('messages.dash.content_field') }}
                    </label>
                    <textarea name="description" id="categoryDesc" class="form-control @error('description') is-invalid @enderror" 
                              rows="2" placeholder="{{ __('messages.admin.categories.placeholder_name') }}"
                              style="border-radius: var(--radius-md); resize: none;">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small uppercase tracking-wider mb-2">
                        {{ __('messages.dash.status_field') }}
                    </label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="categoryActive" value="1" checked>
                        <label class="form-check-label text-dark fw-medium" for="categoryActive">
                            {{ __('messages.admin.categories.active') }}
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary rounded-xl px-4 py-2 text-white shadow-sm font-semibold transition-all hover:opacity-90" style="background-color: var(--brand); border-color: var(--brand); border-radius: var(--radius-md);">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.admin.categories.add') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category List Table -->
<div class="edudash-card border-0 shadow-sm" style="border-radius: var(--radius-xl); background: #ffffff;">
    <div class="edudash-card-header bg-white py-3" style="border-bottom: 1px solid var(--border);">
        <h5 class="edudash-card-title mb-0 text-dark fw-bold d-flex align-items-center">
            <i class="fa-solid fa-list-ul me-2 text-primary" style="color: var(--brand) !important;"></i>
            {{ __('messages.admin.categories.all') }}
        </h5>
    </div>
    <div class="edudash-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">#</th>
                        <th class="text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.admin.categories.icon') }}</th>
                        <th class="text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.admin.categories.name') }}</th>
                        <th class="text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.admin.categories.slug') }}</th>
                        <th class="text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.admin.categories.courses_count') }}</th>
                        <th class="text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.dash.status_field') }}</th>
                        <th class="pe-4 text-end text-muted fw-bold small text-uppercase" style="background-color: #f8fafc; border-bottom: 1px solid var(--border);">{{ __('messages.dash.action_field') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="transition-all hover:bg-light">
                        <td class="ps-4 fw-semibold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-3 border" style="width: 44px; height: 44px; border-color: rgba(15, 23, 42, 0.08); overflow: hidden; padding: 6px;">
                                @if(str_starts_with(trim($category->icon), '<svg'))
                                    <div class="category-svg-wrapper" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: var(--brand);">
                                        {!! $category->icon !!}
                                    </div>
                                    <style>
                                        .category-svg-wrapper svg {
                                            width: 100% !important;
                                            height: 100% !important;
                                            max-width: 28px !important;
                                            max-height: 28px !important;
                                            fill: currentColor !important;
                                        }
                                    </style>
                                @else
                                    <i class="{{ $category->icon ?? 'fas fa-laptop-code' }} text-primary fs-5" style="color: var(--brand) !important;"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-dark fw-bold fs-6">{{ $category->name }}</span>
                            @if($category->description)
                                <div class="text-muted small text-truncate" style="max-width: 280px;">{{ $category->description }}</div>
                            @endif
                        </td>
                        <td>
                            <code class="px-2 py-1 bg-light text-primary rounded font-mono" style="font-size: 13px; color: var(--brand) !important; background-color: var(--brand-soft) !important;">{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-xl fw-bold">
                                <i class="fas fa-graduation-cap me-1 text-secondary"></i>
                                {{ $category->courses_count }}
                            </span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-xl fw-bold">
                                    {{ __('messages.admin.categories.active') }}
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-xl fw-bold">
                                    {{ __('messages.admin.categories.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-inline-flex gap-2">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="btn btn-sm btn-outline-warning rounded-xl px-3 py-2 transition-all d-inline-flex align-items-center"
                                   style="border-radius: var(--radius-md);" title="{{ __('messages.admin.categories.edit_btn') }}">
                                    <i class="fas fa-edit me-1"></i> {{ __('messages.admin.categories.edit_btn') }}
                                </a>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-danger rounded-xl px-3 py-2 transition-all d-inline-flex align-items-center"
                                            style="border-radius: var(--radius-md);"
                                            onclick="return confirm('{{ __('messages.dash.confirm_delete_q') }}')">
                                        <i class="fas fa-trash me-1"></i> {{ __('messages.admin.categories.delete_btn') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <div class="py-4">
                                <i class="fa-solid fa-folder-open fs-1 text-muted mb-3 opacity-50"></i>
                                <p class="mb-0">{{ __('messages.admin.categories.no_categories') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $categories->links() }}
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('categoryName');
        const slugInput = document.getElementById('categorySlug');
        const iconInput = document.getElementById('categoryIcon');
        const iconPreview = document.getElementById('iconPreview');

        if (nameInput && slugInput) {
            nameInput.addEventListener('input', function() {
                // simple slugify function
                const slug = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9а-яё-]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                slugInput.placeholder = slug || '{{ __("messages.admin.categories.placeholder_slug") }}';
            });
        }

        if (iconInput && iconPreview) {
            iconInput.addEventListener('input', function() {
                iconPreview.className = iconInput.value || 'fas fa-laptop-code';
            });
        }
    });
</script>
@endpush