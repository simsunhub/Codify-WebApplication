@extends('admin.layouts.app')

@section('title', __('messages.dash.add_announcement'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.dash.title_field') }} *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="{{ __('messages.announcements.title_placeholder') }}">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.dash.content_field') }} *</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror"
                                  rows="5" placeholder="{{ __('messages.announcements.content_placeholder') }}">{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.announcements.target_audience') }} *</label>
                        <select name="target_role" class="form-select @error('target_role') is-invalid @enderror">
                            <option value="all" {{ old('target_role') == 'all' ? 'selected' : '' }}>
                                🌐 {{ __('messages.announcements.target_all') }}
                            </option>
                            <option value="student_only" {{ old('target_role') == 'student_only' ? 'selected' : '' }}>
                                🎓 {{ __('messages.announcements.target_students') }}
                            </option>
                            <option value="teacher_only" {{ old('target_role') == 'teacher_only' ? 'selected' : '' }}>
                                👨‍🏫 {{ __('messages.announcements.target_teachers') }}
                            </option>
                        </select>
                        @error('target_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">{{ __('messages.admin.active') }}</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('messages.dash.publish') }}
                        </button>
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">{{ __('messages.dash.back') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
