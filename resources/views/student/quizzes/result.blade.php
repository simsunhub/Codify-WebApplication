@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 100px; padding-bottom: 60px; max-width: 900px; margin: 0 auto;">
    
    <!-- Result Header Card -->
    <div class="glass-card" style="background: rgba(10, 10, 20, 0.6); border: 1px solid {{ $attempt->passed ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)' }}; border-radius: 24px; padding: 40px; text-align: center; margin-bottom: 32px; backdrop-filter: blur(16px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), 0 0 40px {{ $attempt->passed ? 'rgba(16, 185, 129, 0.05)' : 'rgba(239, 68, 68, 0.05)' }};">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: {{ $attempt->passed ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: {{ $attempt->passed ? '#10b981' : '#ef4444' }};">
            <i class="fas {{ $attempt->passed ? 'fa-check-circle' : 'fa-times-circle' }}" style="font-size: 36px;"></i>
        </div>
        
        <h1 style="font-size: 28px; font-weight: 800; color: #fff; margin: 0 0 8px 0;">
            {{ $attempt->passed ? (__('messages.quizzes.passed_title') ?? 'Congratulations, You Passed!') : (__('messages.quizzes.failed_title') ?? 'Quiz Attempt Completed') }}
        </h1>
        <p style="color: var(--text-muted, #64748b); font-size: 15px; margin: 0 0 24px 0;">
            {{ $attempt->quiz->title }}
        </p>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; max-width: 500px; margin: 0 auto; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 24px;">
            <div>
                <span style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">{{ __('messages.quizzes.score_lbl') ?? 'Your Score' }}</span>
                <p style="font-size: 32px; font-weight: 800; color: {{ $attempt->passed ? '#10b981' : '#ef4444' }}; margin: 6px 0 0 0;">{{ round($attempt->score, 1) }}%</p>
            </div>
            <div>
                <span style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">{{ __('messages.quizzes.points_lbl') ?? 'Points' }}</span>
                <p style="font-size: 32px; font-weight: 800; color: #fff; margin: 6px 0 0 0;">{{ $attempt->earned_points }} / {{ $attempt->total_points }}</p>
            </div>
            <div>
                <span style="font-size: 13px; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">{{ __('messages.quizzes.passing_lbl') ?? 'Passing Score' }}</span>
                <p style="font-size: 32px; font-weight: 800; color: rgba(255,255,255,0.6); margin: 6px 0 0 0;">{{ $attempt->quiz->pass_percentage }}%</p>
            </div>
        </div>

        <div style="margin-top: 32px;">
            <a href="{{ route('student.quizzes.show', $attempt->quiz_id) }}" class="btn btn-outline" style="padding: 10px 24px; font-size: 14.5px; text-decoration: none;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> {{ __('messages.quizzes.back_to_quiz') ?? 'Back to Quiz' }}
            </a>
        </div>
    </div>

    <!-- Detailed Review -->
    <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 20px;">
        {{ __('messages.quizzes.detailed_review') ?? 'Detailed Question Review' }}
    </h2>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        @foreach($attempt->quiz->questions as $index => $q)
            @php
                // Find user's answer for this question
                $userAnswer = $attempt->answers->where('question_id', $q->id)->first();
                $isCorrect = $userAnswer ? $userAnswer->is_correct : false;
                $selectedOptionId = $userAnswer ? $userAnswer->option_id : null;
            @endphp
            
            <div class="glass-card" style="background: rgba(10, 10, 20, 0.6); border: 1px solid {{ $isCorrect ? 'rgba(16, 185, 129, 0.15)' : ($selectedOptionId ? 'rgba(239, 68, 68, 0.15)' : 'rgba(255,255,255,0.06)') }}; border-radius: 20px; padding: 24px; backdrop-filter: blur(16px);">
                <!-- Question Title & Points -->
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 20px; margin-bottom: 16px;">
                    <div style="display: flex; gap: 12px; align-items: start;">
                        <span style="font-size: 14px; font-weight: 700; color: var(--brand, #f97316); background: rgba(249, 115, 22, 0.1); width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {{ $index + 1 }}
                        </span>
                        <h3 style="font-size: 16px; font-weight: 700; color: #fff; margin: 4px 0 0 0; line-height: 1.4;">
                            {{ $q->question }}
                        </h3>
                    </div>
                    
                    <span style="font-size: 12px; font-weight: 700; color: {{ $isCorrect ? '#10b981' : '#ef4444' }}; background: {{ $isCorrect ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; white-space: nowrap;">
                        {{ $isCorrect ? "+ {$q->points} pts" : "0 / {$q->points} pts" }}
                    </span>
                </div>

                <!-- Options List -->
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 16px;">
                    @foreach($q->options as $option)
                        @php
                            $isSelected = $option->id === $selectedOptionId;
                            $isOptionCorrect = $option->is_correct;
                            
                            $optionBg = 'rgba(255, 255, 255, 0.02)';
                            $optionBorder = 'rgba(255, 255, 255, 0.06)';
                            $optionColor = 'rgba(255, 255, 255, 0.8)';
                            
                            if ($isSelected) {
                                if ($isCorrect) {
                                    $optionBg = 'rgba(16, 185, 129, 0.08)';
                                    $optionBorder = 'rgba(16, 185, 129, 0.3)';
                                    $optionColor = '#10b981';
                                } else {
                                    $optionBg = 'rgba(239, 68, 68, 0.08)';
                                    $optionBorder = 'rgba(239, 68, 68, 0.3)';
                                    $optionColor = '#ef4444';
                                }
                            } elseif ($isOptionCorrect && $selectedOptionId !== null) {
                                // Highlight the correct option in green if user got it wrong
                                $optionBg = 'rgba(16, 185, 129, 0.03)';
                                $optionBorder = 'rgba(16, 185, 129, 0.15)';
                                $optionColor = '#10b981';
                            }
                        @endphp
                        
                        <div style="display: flex; align-items: center; justify-content: space-between; background: {{ $optionBg }}; border: 1px solid {{ $optionBorder }}; border-radius: 12px; padding: 14px 16px; color: {{ $optionColor }}; font-size: 14px; font-weight: 500;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid {{ $isSelected ? ($isCorrect ? '#10b981' : '#ef4444') : ($isOptionCorrect && $selectedOptionId !== null ? '#10b981' : 'rgba(255,255,255,0.2)') }}; display: flex; align-items: center; justify-content: center; background: {{ $isSelected ? ($isCorrect ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)') : 'transparent' }}; flex-shrink:0;">
                                    @if($isSelected)
                                        <i class="fas {{ $isCorrect ? 'fa-check' : 'fa-times' }}" style="font-size: 10px;"></i>
                                    @elseif($isOptionCorrect && $selectedOptionId !== null)
                                        <i class="fas fa-check" style="font-size: 10px;"></i>
                                    @endif
                                </div>
                                <span>{{ $option->option_text }}</span>
                            </div>
                            
                            @if($isSelected)
                                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $isCorrect ? '#10b981' : '#ef4444' }};">
                                    {{ __('messages.quizzes.your_answer') ?? 'Your Answer' }}
                                </span>
                            @elseif($isOptionCorrect && $selectedOptionId !== null)
                                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #10b981;">
                                    {{ __('messages.quizzes.correct_answer') ?? 'Correct' }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Explanation Box -->
                @if($q->explanation)
                    <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 12px; padding: 14px 16px; margin-top: 12px; display: flex; gap: 12px; align-items: start;">
                        <i class="fas fa-lightbulb" style="color: var(--brand, #f97316); margin-top: 2px;"></i>
                        <div>
                            <span style="font-size: 12.5px; font-weight: 700; color: #fff; display: block; margin-bottom: 4px;">{{ __('messages.quizzes.explanation_lbl') ?? 'Explanation' }}</span>
                            <p style="margin: 0; font-size: 13px; color: var(--text-muted, #64748b); line-height: 1.5;">
                                {{ $q->explanation }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

</div>
@endsection