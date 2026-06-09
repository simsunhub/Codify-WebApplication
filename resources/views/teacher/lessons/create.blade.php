@extends('teacher.layouts.app')
@section('title', __('Lesson Kosh'))
@section('breadcrumb', __('Lesson Kosh'))

@section('page-actions')
<a href="{{ route('teacher.lessons.index') }}" class="ed-btn ed-btn-outline">
    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title">
                        <i class="fa-solid fa-circle-plus me-2" style="color:var(--brand);"></i>{{ __('Lesson Kosh') }}
                    </div>
                    <div class="ed-card-subtitle">Add a new lesson to your course with description and optional video playback</div>
                </div>
            </div>
            <div class="ed-card-body">
                <form action="{{ route('teacher.lessons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row g-4">
                        {{-- Title --}}
                        <div class="col-md-8">
                            <label class="ed-form-label">{{ __('Lesson name') }} *</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="{{ __('Lesson name') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Course Select --}}
                        <div class="col-md-4">
                            <label class="ed-form-label">{{ __('messages.dash.course') }} *</label>
                            <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">{{ __('Choose') }}...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Content Description --}}
                        <div class="col-12">
                            <label class="ed-form-label">{{ __('Content') }}</label>
                            <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="5" placeholder="Describe the lesson content... (HTML is supported)">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Video Download --}}
                        <div class="col-12">
                            <label class="ed-form-label">
                                <i class="fas fa-video me-1" style="color:var(--green);"></i>{{ __('Video download') }}
                            </label>
                            <input type="file" name="video" class="form-control @error('video') is-invalid @enderror"
                                   accept="video/mp4,video/avi,video/mov" onchange="previewVideo(this)">
                            <div class="form-text text-muted mt-2" style="font-size: 12px;">
                                Supported formats: MP4, AVI, MOV. {{ __('Maximum') }} 200MB.
                            </div>
                            @error('video')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div id="videoPreview" class="mt-3 d-none p-3 rounded" style="background: rgba(255,255,255,.02); border:1px solid var(--card-border);">
                                <video id="previewPlayer" controls class="w-100 rounded" style="max-height:320px; outline: none; background:#000;">
                                    <source id="previewSource" src="" type="video/mp4">
                                </video>
                                <div class="mt-2 d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-file-video text-success"></i>
                                    <span id="videoFileName" class="text-muted small"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Order --}}
                        <div class="col-md-4">
                            <label class="ed-form-label">{{ __('Order') }}</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                                   value="{{ old('order', 1) }}" min="1">
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Switches --}}
                        <div class="col-12 py-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label text-muted ms-2" for="is_active" style="font-size:13.5px; font-weight:600;">
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" name="is_preview" id="is_preview" value="1">
                                <label class="form-check-label text-muted ms-2" for="is_preview" style="font-size:13.5px; font-weight:600;">
                                    {{ __('Free viewing') }} ({{ __('View') }} / Free Trial Preview)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer Actions --}}
                    <div class="d-flex gap-3 border-top mt-4 pt-4">
                        <button type="submit" class="ed-btn ed-btn-primary">
                            <i class="fas fa-save me-1"></i>{{ __('Save') }}
                        </button>
                        <a href="{{ route('teacher.lessons.index') }}" class="ed-btn ed-btn-outline">
                            {{ __('Back') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewVideo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const url = URL.createObjectURL(file);
        const previewWrap = document.getElementById('videoPreview');
        const previewPlayer = document.getElementById('previewPlayer');
        const previewSource = document.getElementById('previewSource');
        const fileNameSpan = document.getElementById('videoFileName');
        
        previewSource.src = url;
        previewPlayer.load();
        fileNameSpan.textContent = file.name + ' (' + (file.size / (1024 * 1024)).toFixed(1) + ' MB)';
        previewWrap.classList.remove('d-none');
    }
}
</script>
@endsection