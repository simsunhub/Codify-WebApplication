@extends('teacher.layouts.app')
@section('title', __('Administer Test Questions') . ' - ' . $quiz->title)
@section('breadcrumb', $quiz->title)

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="background:rgba(16,185,129,.1);color:var(--green);border:1px solid rgba(16,185,129,.2);border-radius:var(--radius-md);">
    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter:invert(1);"></button>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="ed-card" style="background:var(--card-bg);border:1px solid var(--card-border);border-radius:var(--radius-md);">
            <div class="ed-card-header" style="border-bottom:1px solid var(--card-border);">
                <div class="ed-card-title"><i class="fa-solid fa-list-check me-2" style="color:var(--brand);"></i>{{ __('Test questions') }} ({{ $questions->count() }})</div>
            </div>
            <div class="ed-card-body d-flex flex-column gap-4" style="padding:20px;">
                @forelse($questions as $question)
                    <div style="padding:20px;border:1.5px solid var(--card-border);border-radius:var(--radius-md);background:var(--card-bg2);position:relative;margin-bottom:16px;">
                        <div style="position:absolute;right:20px;top:20px;display:flex;align-items:center;gap:8px;">
                            <span class="ed-badge ed-badge-indigo" style="font-size:11.5px;background:rgba(99, 102, 241, 0.1);color:#6366f1;">{{ $question->points }} {{ __('point') }}</span>
                        </div>
                        <h5 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;max-width:85%;">
                            {{ $loop->iteration }}. {{ $question->question }}
                        </h5>
                        
                        <div class="row g-2">
                            @foreach($question->options as $index => $option)
                                <div class="col-sm-6">
                                    <div style="padding:10px 12px;border-radius:var(--radius-md);font-size:13px;
                                                @if($option->is_correct)
                                                    background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.35);color:var(--green);font-weight:700;
                                                @else
                                                    background:rgba(255,255,255,.03);border:1px solid var(--card-border);color:var(--text-muted);
                                                @endif">
                                        <i class="fa-solid @if($option->is_correct) fa-circle-check @else fa-circle @endif me-2"></i>
                                        {{ $option->option_text }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($question->explanation)
                            <div class="mt-3" style="font-size:12.5px;color:var(--text-muted);background:rgba(255,255,255,.02);padding:10px 12px;border-radius:var(--radius-md);border-left:2.5px solid var(--brand);">
                                <strong>{{ __('Explanation') }}:</strong> {{ $question->explanation }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div style="font-size:40px;margin-bottom:14px;">📝</div>
                        <div style="font-weight:700;color:var(--text);font-size:15px;">{{ __('Questions have not been created') }}</div>
                        <div style="color:var(--text-muted);font-size:13px;margin-top:6px;">{{ __('Add questions using the form on the right to run the quiz') }}.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        {{-- Add new question form --}}
        <div class="ed-card h-100">
            <div class="ed-card-header">
                <div>
                    <div class="ed-card-title"><i class="fa-solid fa-plus me-2" style="color:var(--brand);"></i>{{ __('Add a new question') }}</div>
                    <div class="ed-card-subtitle">{{ __('Write the test question and answer options') }}</div>
                </div>
            </div>

            <div class="ed-card-body">
                <form action="{{ route('teacher.quizzes.questions.store', $quiz->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Question text') }} <span class="text-danger">*</span></label>
                        <textarea name="question" rows="3" class="form-control" placeholder="{{ __('Write the content of the question...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>{{ old('question') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('This is the point of the question') }} <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" value="10" min="1" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;color:var(--text);">{{ __('Explanation of the correct answer') }} ({{ __('optional') }})</label>
                        <textarea name="explanation" rows="2" class="form-control" placeholder="{{ __('The explanation that the student will see after completing the test...') }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);">{{ old('explanation') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight:700;color:var(--text);display:block;margin-bottom:12px;">{{ __('Answer options') }} ({{ __('minimal') }} 2, {{ __('choose the right one') }}):</label>
                        
                        <div class="d-flex flex-column gap-3">
                            @for($i = 0; $i < 4; $i++)
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-check-inline" style="margin:0;">
                                        <input class="form-check-input" type="radio" name="correct_option" id="correctOpt{{ $i }}" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} style="transform:scale(1.2);accent-color:var(--brand);cursor:pointer;">
                                    </div>
                                    <input type="text" name="options[]" class="form-control" placeholder="{{ __('Option') }} {{ $i+1 }}" style="background:var(--card-bg2);border:1px solid var(--card-border);color:var(--text);" {{ $i < 2 ? 'required' : '' }}>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <button type="submit" class="ed-btn w-100" style="background:var(--brand);color:#fff;border:0;">
                        <i class="fa-solid fa-plus"></i> {{ __('Add question') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection