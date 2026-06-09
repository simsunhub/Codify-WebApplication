@extends('teacher.layouts.app')
@section('title', __('Create a New Issue'))
@section('breadcrumb', __('New Issue'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="ed-card">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new issue') }}</div>
                    <div class="ed-card-subtitle">{{ __('Coding problem conditions') }}, {{ __('Enter the difficulty and source code') }}</div>
                </div>
                <a href="{{ route('teacher.coding.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                    <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.coding.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Issue title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="{{ __('For example: The sum of two numbers') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Difficulty') }} <span class="text-danger">*</span></label>
                            <select name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                                <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>{{ __('Easy') }} (Easy)</option>
                                <option value="medium" {{ old('difficulty', 'medium') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }} (Medium)</option>
                                <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>{{ __('Difficult') }} (Hard)</option>
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-4">
                            <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Category') }}</label>
                            <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}" placeholder="{{ __('For example: Arrays, Strings') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Description of the condition') }} (Description) <span class="text-danger">*</span></label>
                        <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('Information about the purpose of the problem and under what conditions it will work...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Restrictions') }} (Constraints)</label>
                        <textarea name="constraints" rows="3" class="form-control @error('constraints') is-invalid @enderror" placeholder="{{ __('For example: 1 <= nums.length <= 10^4') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('constraints') }}</textarea>
                        @error('constraints')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    
                    <div class="mb-4">
                        <label class="form-label" style="font-weight:700;color:var(--text);display:block;margin-bottom:12px;">{{ __('Source code') }} (Starter Code) <span class="text-danger">*</span></label>
                        
                        <ul class="nav nav-tabs border-bottom-0 mb-3" role="tablist">
                            @foreach($languages as $index => $lang)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $lang->slug }}" data-bs-toggle="tab" data-bs-target="#panel-{{ $lang->slug }}" type="button" role="tab" style="background:transparent;border:0;color:var(--text-muted);font-weight:600;padding:10px 16px;border-bottom:2px solid transparent;">
                                        <i class="fa-solid fa-file-code me-2"></i> {{ $lang->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content" style="background:var(--card-bg2);border:1px solid var(--card-border);border-radius:var(--radius-lg);padding:18px;">
                            @foreach($languages as $index => $lang)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="panel-{{ $lang->slug }}" role="tabpanel">
                                    <div class="mb-2" style="font-size:12.5px;color:var(--text-muted);">{{ $lang->name }} {{ __('function template in language') }}:</div>
                                    <textarea name="starter_code[{{ $lang->slug }}]" rows="8" class="form-control code-editor" placeholder="{{ $lang->slug === 'python' ? 'def solution(a, b):' : ($lang->slug === 'javascript' ? 'function solution(a, b) {' : 'class Solution {') }}" style="font-family:'Courier New', monospace;background:#0d0d0d;color:#10b981;border:1px solid rgba(255,255,255,.05);"></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="{{ route('teacher.coding.index') }}" class="ed-btn" style="background:rgba(255,255,255,.05);color:var(--text);border:1px solid var(--card-border);">
                            {{ __('Cancellation') }}
                        </a>
                        <button type="submit" class="ed-btn" style="background:var(--brand);color:#fff;border:0;">
                            <i class="fa-solid fa-save"></i> {{ __('Save the issue') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.nav-tabs .nav-link.active {
    color: var(--brand) !important;
    border-bottom: 2px solid var(--brand) !important;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hintsContainer = document.getElementById('hintsContainer');
    const addHintBtn = document.getElementById('addHintBtn');

    addHintBtn.addEventListener('click', function() {
        const hintCount = hintsContainer.children.length;
        const newHint = document.createElement('div');
        newHint.className = 'd-flex gap-2';
        newHint.innerHTML = `
            <input type="text" name="hints[]" class="form-control" placeholder="${hintCount + 1}-{{ __('hint') }}..." style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
            <button type="button" class="btn btn-danger btn-sm remove-hint-btn"><i class="fa-solid fa-trash"></i></button>
        `;
        hintsContainer.appendChild(newHint);

        // Bind delete action
        newHint.querySelector('.remove-hint-btn').addEventListener('click', function() {
            newHint.remove();
            reindexHints();
        });
    });

    function reindexHints() {
        Array.from(hintsContainer.children).forEach((child, index) => {
            const input = child.querySelector('input');
            input.placeholder = `${index + 1}-{{ __('hint') }}...`;
        });
    }
});
</script>
@endsection