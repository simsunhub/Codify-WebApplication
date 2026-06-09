@extends('admin.layouts.app')

@section('title', __('messages.admin.edit_user'))

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{ __('messages.admin.edit_user') }}</h2>
        </div>
        <div class="card-body">
            <div style="margin-bottom: 24px; padding: 16px; background: var(--bg); border-radius: var(--radius-md);">
                <strong style="color: var(--text-primary);">{{ __('messages.admin.name') }}:</strong> <span style="color: var(--text-secondary);">{{ $user->name }}</span><br>
                <strong style="color: var(--text-primary);">Email:</strong> <span style="color: var(--text-secondary);">{{ $user->email }}</span>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label class="form-label">{{ __('messages.admin.role') }}</label>
                    @if($user->id !== auth()->id())
                        <select name="role" class="form-control">
                            <option value="student" {{ $user->isStudent() ? 'selected' : '' }}>{{ __('messages.admin.role_student') }}</option>
                            <option value="teacher" {{ $user->isTeacher() ? 'selected' : '' }}>{{ __('messages.admin.role_teacher') }}</option>
                            <option value="admin" {{ $user->isAdmin() ? 'selected' : '' }}>{{ __('messages.admin.role_admin') }}</option>
                        </select>
                    @else
                        <select name="role" class="form-control" disabled style="opacity: 0.7; cursor: not-allowed;" title="{{ __('messages.admin.cannot_change_own_role') }}">
                            <option value="admin" selected>{{ __('messages.admin.role_admin') }}</option>
                        </select>
                        <input type="hidden" name="role" value="admin">
                        <small class="text-muted" style="display: block; margin-top: 6px; color: var(--brand) !important; font-size: 12px; font-weight: 500;">
                            <i class="fas fa-exclamation-triangle"></i> {{ __('messages.admin.self_demotion_warning') }}
                        </small>
                    @endif
                </div>
                <div class="form-group" style="margin-top: 20px; margin-bottom: 20px;">
                    <label class="form-label" style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px; background: rgba(245, 158, 11, 0.05); border: 1px dashed rgba(245, 158, 11, 0.3); border-radius: var(--radius-md);">
                        <input type="checkbox" name="is_premium" value="1" {{ $user->is_premium ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #f59e0b;">
                        <div>
                            <span style="font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 6px;">
                                <i class="fas fa-crown" style="color: #f59e0b;"></i>
                                {{ __('messages.admin.modules.premium_access') }}
                            </span>
                            <small class="text-muted" style="display: block; font-size: 12px; margin-top: 2px;">
                                {{ __('messages.admin.modules.premium_access_desc') }}
                            </small>
                        </div>
                    </label>
                </div>
                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save" style="margin-right: 8px;"></i>{{ __('messages.dash.save') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">{{ __('messages.dash.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
