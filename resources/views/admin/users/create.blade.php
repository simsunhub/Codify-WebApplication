@extends('admin.layouts.app')

@section('title', __('Add New User'))

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">{{ __('Add New User') }}</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4" style="border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border: none; padding: 16px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="user@example.com" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Role') }}</label>
                    <select name="role" class="form-control" required>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>{{ __('Instructor') }}</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i>{{ __('Create User') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection