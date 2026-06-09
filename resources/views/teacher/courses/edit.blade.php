@extends('teacher.layouts.app')
@section('title', 'Edit Course')
@section('breadcrumb', 'Edit Course')

@section('page-actions')
<a href="{{ route('teacher.courses.index') }}" class="ed-btn ed-btn-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to List
</a>
@endsection

@section('content')
<form action="{{ route('teacher.courses.update', $course) }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    {{-- Validation summary --}}
    @if($errors->any())
    <div class="ed-alert ed-alert-error mb-4">
        <i class="fa-solid fa-circle-exclamation" style="font-size:16px;margin-top:1px;"></i>
        <div style="flex:1;">
            <strong>Please fix the errors below before saving.</strong>
            <ul style="margin:6px 0 0;padding-left:18px;font-size:13px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        <button class="ed-alert-close" onclick="this.closest('.ed-alert').remove()" type="button">×</button>
    </div>
    @endif

    <div class="row g-4">

        {{-- ── LEFT COLUMN ────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Course Details card --}}
            <div class="ed-card mb-4">
                <div class="ed-card-header">
                    <div>
                        <div class="ed-card-title"><i class="fa-solid fa-pen-to-square me-2" style="color:var(--brand);"></i>Course Information</div>
                        <div class="ed-card-subtitle">Editing: {{ $course->title }}</div>
                    </div>
                </div>
                <div class="ed-card-body">
                    <div class="row g-4">

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="courseTitle">
                                Course Title <span style="color:var(--red);">*</span>
                            </label>
                            <input type="text"
                                   id="courseTitle"
                                   name="title"
                                   class="ed-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                                   value="{{ old('title', $course->title) }}"
                                   placeholder="e.g. Complete Web Development Bootcamp 2024"
                                   maxlength="255">
                            @error('title')
                                <div class="ed-feedback"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="courseDesc">Course Description</label>
                            <textarea id="courseDesc"
                                      name="description"
                                      class="ed-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                      rows="5"
                                      placeholder="Describe what students will learn…">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <div class="ed-feedback"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Thumbnail card --}}
            <div class="ed-card">
                <div class="ed-card-header">
                    <div>
                        <div class="ed-card-title"><i class="fa-solid fa-image me-2" style="color:var(--brand);"></i>Course Thumbnail</div>
                        <div class="ed-card-subtitle">Upload a new image to replace the current one</div>
                    </div>
                </div>
                <div class="ed-card-body">
                    {{-- Current thumbnail preview --}}
                    @if($course->image_path || $course->image)
                    <div style="margin-bottom:16px;">
                        <div style="font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.06em;">Current Thumbnail</div>
                        <img src="{{ asset('storage/' . ($course->image_path ?? $course->image)) }}"
                             alt="{{ $course->title }}"
                             style="width:180px;height:120px;object-fit:cover;border-radius:var(--radius-lg);border:2px solid rgba(99, 102, 241,.15);">
                    </div>
                    @endif

                    <div class="ed-dropzone" id="dropzone">
                        <input type="file" name="image" id="thumbInput" accept="image/*">
                        <div id="ed-dz-content">
                            <div class="ed-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="ed-dropzone-text">Drag & drop a new thumbnail</div>
                            <div class="ed-dropzone-hint">or <u style="color:var(--brand);">click to browse</u> — leaves current if empty</div>
                        </div>
                        <div id="ed-preview-wrap">
                            <img id="ed-preview-img" src="" alt="Preview">
                            <div style="font-size:12.5px;color:var(--text-muted);margin-top:8px;" id="ed-preview-name"></div>
                        </div>
                    </div>
                    @error('image')
                        <div class="ed-feedback mt-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Settings card --}}
            <div class="ed-card mb-4">
                <div class="ed-card-header">
                    <div class="ed-card-title"><i class="fa-solid fa-sliders me-2" style="color:var(--brand);"></i>Settings</div>
                </div>
                <div class="ed-card-body">
                    <div class="row g-3">

                        {{-- Category --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="courseCategory">
                                Category <span style="color:var(--red);">*</span>
                            </label>
                            <select id="courseCategory"
                                    name="category_id"
                                    class="ed-select {{ $errors->has('category_id') ? 'is-invalid' : '' }}">
                                <option value="">— Select a category —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="ed-feedback"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Price --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="coursePrice">
                                Price <span style="color:var(--red);">*</span>
                            </label>
                            <div class="ed-input-group">
                                <span class="ed-input-prefix">$</span>
                                <input type="number"
                                       id="coursePrice"
                                       name="price"
                                       class="ed-input {{ $errors->has('price') ? 'is-invalid' : '' }}"
                                       value="{{ old('price', $course->price) }}"
                                       min="0" max="9999.99" step="0.01">
                            </div>
                            @error('price')
                                <div class="ed-feedback"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Level --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="courseLevel">Difficulty Level</label>
                            <select id="courseLevel" name="level" class="ed-select">
                                <option value="beginner"     {{ old('level', $course->level) === 'beginner'     ? 'selected' : '' }}>🌱 Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>🚀 Intermediate</option>
                                <option value="advanced"     {{ old('level', $course->level) === 'advanced'     ? 'selected' : '' }}>🔥 Advanced</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-12">
                            <label class="ed-form-label" for="courseStatus">Publication Status</label>
                            <select id="courseStatus" name="status" class="ed-select">
                                <option value="draft"     {{ old('status', $course->status) === 'draft'     ? 'selected' : '' }}>📝 Draft (Hidden)</option>
                                <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>✅ Published (Live)</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Save card --}}
            <div class="ed-card">
                <div class="ed-card-body" style="display:flex;flex-direction:column;gap:10px;">
                    <button type="submit" class="ed-btn ed-btn-primary w-100" style="justify-content:center;">
                        <i class="fa-solid fa-floppy-disk"></i> Update Course
                    </button>
                    <a href="{{ route('teacher.courses.index') }}" class="ed-btn ed-btn-outline w-100" style="justify-content:center;">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
const thumbInput   = document.getElementById('thumbInput');
const previewWrap  = document.getElementById('ed-preview-wrap');
const previewImg   = document.getElementById('ed-preview-img');
const previewName  = document.getElementById('ed-preview-name');
const dzContent    = document.getElementById('ed-dz-content');

thumbInput?.addEventListener('change', function () {
    const file = this.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        previewImg.src = e.target.result;
        previewName.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        dzContent.style.display  = 'none';
        previewWrap.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
