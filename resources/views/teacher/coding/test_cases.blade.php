@extends('teacher.layouts.app')
@section('title', __('Managing Code Tests'))
@section('breadcrumb', __('Problem Tests'))

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(16,185,129,.1);color:var(--green);border:1px solid rgba(16,185,129,.2);border-radius:var(--radius-md);">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);"></button>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new test') }}</div>
                    <div class="ed-card-subtitle">{{ __('Fill in the input and expected results') }}</div>
                </div>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.coding.test-cases.store', $problem->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Incoming information') }} (Input) <span class="text-danger">*</span></label>
                        <textarea name="input" rows="4" class="form-control" placeholder="{{ __('For example: 5 10') }}" style="font-family:'Courier New', monospace;background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('input') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Expected result') }} (Expected Output) <span class="text-danger">*</span></label>
                        <textarea name="expected_output" rows="4" class="form-control" placeholder="{{ __('For example: 15') }}" style="font-family:'Courier New', monospace;background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('expected_output') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Sample testing') }} ({{ __('It appears to students') }})</label>
                        <select name="is_sample" class="form-select" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">
                            <option value="0">{{ __('Are not') }} ({{ __('For a secret check') }})</option>
                            <option value="1">{{ __('Oh yes') }} ({{ __('An example is shown on the problem page') }})</option>
                        </select>
                    </div>

                    <button type="submit" class="ed-btn w-100" style="background:var(--brand);color:#fff;border:0;">
                        <i class="fa-solid fa-plus"></i> {{ __('Add a test') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection