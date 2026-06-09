@extends('layouts.app')

@section('extra-css')
<style>
    .form-control {
        background: rgba(255, 255, 255, 0.03) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        transition: all 0.2s ease !important;
        width: 100%;
    }
    .form-control:focus {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        outline: none !important;
    }
</style>
@endsection

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px;">
    @include('student.layouts.nav')

    <div style="margin-bottom: 30px;">
        <a href="{{ route('student.assignments.index') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; background: rgba(255, 255, 255, 0.02); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'; this.style.borderColor='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'; this.style.borderColor='rgba(255,255,255,0.15)'">
            <i class="fas fa-arrow-left me-2"></i> {{ __('Back to Assignments') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 16px; background: rgba(16, 185, 129, 0.1); color: #047857;">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Assignment Details -->
        <div class="col-lg-7">
            <div class="glass-card" style="padding: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 16px;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: var(--brand); text-transform: uppercase; letter-spacing: 0.5px;">{{ $assignment->course->title }}</span>
                        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-top: 4px;">{{ $assignment->title }}</h1>
                    </div>
                    <span class="badge bg-secondary" style="font-size: 13px; padding: 6px 12px;">{{ $assignment->max_score }} pts max</span>
                </div>

                <div style="color: var(--text-primary); font-size: 15px; line-height: 1.6; margin-bottom: 28px;">
                    <h5 style="color: #fff; font-weight: 700; margin-bottom: 10px;">{{ __('Description') }}</h5>
                    <p style="white-space: pre-wrap;">{{ $assignment->description }}</p>
                </div>

                @if($assignment->instructions)
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.04); border-radius: 12px; padding: 20px; margin-bottom: 28px;">
                        <h5 style="color: #fff; font-weight: 700; margin-bottom: 8px; font-size: 14.5px;">
                            <i class="fa-solid fa-circle-info text-primary me-2"></i> {{ __('Submission Instructions') }}
                        </h5>
                        <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.5; margin: 0; white-space: pre-wrap;">{{ $assignment->instructions }}</p>
                    </div>
                @endif

                <div class="row g-3" style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 20px; font-size: 13.5px;">
                    <div class="col-sm-6">
                        <span class="text-muted d-block">{{ __('Due Date') }}</span>
                        <strong style="color: #fff;">{{ $assignment->due_date ? $assignment->due_date->format('M d, Y H:i') : __('No deadline') }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block">{{ __('File Requirements') }}</span>
                        <strong style="color: #fff;">Max {{ $assignment->max_file_size }}MB ({{ $assignment->allowed_extensions }})</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Status & Upload -->
        <div class="col-lg-5">
            <div class="glass-card" style="padding: 32px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px;">
                    <i class="fas fa-file-upload text-primary me-2"></i> {{ __('Your Submission') }}
                </h3>

                @if($submission)
                    <!-- Graded / Feedback -->
                    @if($submission->status === 'graded')
                        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <strong class="text-success"><i class="fa-solid fa-circle-check"></i> Graded</strong>
                                <span style="font-size: 18px; font-weight: 800; color: #fff;">{{ $submission->score }} / {{ $assignment->max_score }}</span>
                            </div>
                            @if($submission->feedback)
                                <div style="margin-top: 12px; border-top: 1px solid rgba(16, 185, 129, 0.15); padding-top: 12px; font-size: 13.5px;">
                                    <span class="text-muted d-block mb-1">{{ __('Instructor Feedback') }}:</span>
                                    <p style="color: #fff; margin: 0; white-space: pre-wrap;">{{ $submission->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: center;">
                            <strong class="text-info" style="font-size: 15px;"><i class="fa-solid fa-clock"></i> {{ __('Submitted & Pending Grade') }}</strong>
                            <div class="text-muted small mt-1">{{ __('Submitted on') }} {{ $submission->submitted_at->format('M d, Y H:i') }}</div>
                        </div>
                    @endif

                    <!-- Show submitted details -->
                    <div style="margin-bottom: 24px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 16px;">
                        @if($submission->file_name)
                            <div style="margin-bottom: 12px;">
                                <span class="text-muted d-block small mb-1">{{ __('Attached File') }}:</span>
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" style="color: var(--brand); font-weight: 600; text-decoration: none;">
                                    <i class="fa-solid fa-paperclip"></i> {{ $submission->file_name }}
                                </a>
                            </div>
                        @endif

                        @if($submission->content)
                            <div>
                                <span class="text-muted d-block small mb-1">{{ __('Submitted Text') }}:</span>
                                <p style="color: #fff; background: rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.04); border-radius: 8px; padding: 12px; margin: 0; white-space: pre-wrap;">{{ $submission->content }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!$submission || $submission->status !== 'graded')
                    <!-- Submit Form -->
                    <form action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($submission)
                            <input type="hidden" name="old_file_path" value="{{ $submission->file_path }}">
                            <input type="hidden" name="old_file_name" value="{{ $submission->file_name }}">
                        @endif

                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Text Submission') }}</label>
                            <textarea name="content" rows="5" class="form-control" placeholder="Write or paste your submission content here..." style="resize: none;">{{ old('content', $submission->content ?? '') }}</textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="form-label">{{ __('Attach File') }}</label>
                            <input type="file" name="file" class="form-control">
                            @if($submission && $submission->file_name)
                                <small class="text-muted d-block mt-1">{{ __('Leave blank to keep your current file:') }} <strong>{{ $submission->file_name }}</strong></small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-gradient w-100" style="padding: 12px; font-weight: 700; border-radius: 12px;">
                            <i class="fa-solid fa-paper-plane me-2"></i> {{ $submission ? __('Resubmit Task') : __('Submit Task') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection