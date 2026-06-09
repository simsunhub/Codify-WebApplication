@extends('teacher.layouts.app')

@section('title', __('messages.announcements.new_announcement'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('teacher.announcements.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="ed-page-title mb-0">
                    <i class="fa-solid fa-bullhorn me-2" style="color:var(--brand)"></i>
                    {{ __('messages.announcements.new_announcement') }}
                </h1>
                <p class="ed-page-sub mb-0">{{ __('messages.announcements.course_announcement_info') }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="ed-form-card">
            <form action="{{ route('teacher.announcements.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="ed-form-label">{{ __('messages.dash.title_field') }} *</label>
                    <input type="text" name="title"
                           class="ed-form-input @error('title') is-invalid @enderror"
                           value="{{ old('title') }}"
                           placeholder="{{ __('messages.announcements.title_placeholder') }}">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="ed-form-label">{{ __('messages.announcements.select_course') }} *</label>
                    <select name="course_id" class="ed-form-select @error('course_id') is-invalid @enderror">
                        <option value="">— {{ __('messages.announcements.select_course') }} —</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="ed-form-hint">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        {{ __('messages.announcements.course_students_note') }}
                    </small>
                </div>

                <div class="mb-5">
                    <label class="ed-form-label">{{ __('messages.dash.content_field') }} *</label>
                    <textarea name="content"
                              class="ed-form-textarea @error('content') is-invalid @enderror"
                              rows="7"
                              placeholder="{{ __('messages.announcements.content_placeholder') }}">{{ old('content') }}</textarea>
                    @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill">
                        <i class="fa-solid fa-paper-plane me-2"></i>
                        {{ __('messages.announcements.send_announcement') }}
                    </button>
                    <a href="{{ route('teacher.announcements.index') }}" class="btn btn-secondary rounded-pill px-4">
                        {{ __('messages.dash.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-back {
    width: 40px; height: 40px;
    border-radius: var(--radius-md);
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    transition: var(--transition);
    flex-shrink: 0;
}
.btn-back:hover { border-color: var(--brand); color: var(--brand); }
.ed-form-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-xl);
    padding: 2rem;
}
.ed-form-label {
    display: block;
    font-size: .83rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .55rem;
}
.ed-form-input, .ed-form-select, .ed-form-textarea {
    width: 100%;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-md);
    color: var(--text);
    padding: .7rem 1rem;
    font-size: .9rem;
    font-family: inherit;
    transition: var(--transition);
    outline: none;
    appearance: none;
}
.ed-form-input:focus, .ed-form-select:focus, .ed-form-textarea:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 3px rgba(99,102,241,.15);
    background: rgba(255,255,255,.07);
}
.ed-form-select option { background: #1a1a2e; }
.ed-form-textarea { resize: vertical; min-height: 160px; }
.ed-form-hint {
    display: block;
    font-size: .78rem;
    color: var(--text-dim);
    margin-top: .45rem;
}
</style>
@endsection
