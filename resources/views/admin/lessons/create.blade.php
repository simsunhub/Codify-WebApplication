@extends('admin.layouts.app')

@section('title', __('Add a lesson'))

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{ __('New lesson') }}</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 2;">
                        <label class="form-label">{{ __('Lesson title') }} *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="{{ __('For example: Installing Laravel') }}">
                        @error('title')<div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="flex: 1;">
                        <label class="form-label">{{ __('Well') }} *</label>
                        <select name="course_id" class="form-control">
                            <option value="">{{ __('Select') }}...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')<div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Content') }}</label>
                    <textarea name="content" class="form-control" rows="4" placeholder="{{ __('Text content of the lesson...') }}">{{ old('content') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-video" style="color: var(--brand); margin-right: 8px;"></i>{{ __('Upload video') }}
                    </label>
                    <input type="file" name="video" id="videoInput" class="form-control" accept="video/mp4,video/avi,video/mov,video/wmv" onchange="previewVideo(this)">
                    <small style="color: var(--text-muted); font-size: 12px;">MP4, AVI, MOV. {{ __('Max') }}. 200MB.</small>
                    @error('video')<div style="color: var(--danger); font-size: 12px; margin-top: 4px;">{{ $message }}</div>@enderror

                    <div id="videoPreview" style="margin-top: 16px; display: none;">
                        <video id="previewPlayer" controls style="width: 100%; max-height: 300px; border-radius: var(--radius-md); border: 2px solid var(--brand);">
                            <source id="previewSource" src="" type="video/mp4">
                        </video>
                    </div>
                </div>

                <div class="form-group" style="max-width: 200px;">
                    <label class="form-label">{{ __('Serial number') }}</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', 1) }}" min="1">
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" value="1" checked id="isActiveCheck">
                    <label class="form-label" for="isActiveCheck" style="margin-bottom: 0;">{{ __('Active') }}</label>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" style="margin-right: 8px;"></i>{{ __('Save') }}
                    </button>
                    <a href="{{ route('admin.lessons.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewVideo(input) {
    const preview = document.getElementById('videoPreview');
    const source = document.getElementById('previewSource');
    const player = document.getElementById('previewPlayer');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const url = URL.createObjectURL(file);

        source.src = url;
        player.load();
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection