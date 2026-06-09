<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizOption;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $courseIds = Enrollment::where('user_id', $userId)->pluck('course_id');

        $quizzes = Quiz::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with(['course', 'attempts' => function ($query) use ($userId) {
                $query->where('user_id', $userId)->orderBy('completed_at', 'desc');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('student.quizzes.index', compact('quizzes'));
    }

    public function show($id)
    {
        $userId = auth()->id();
        $quiz = Quiz::where('is_published', true)->with('questions.options')->findOrFail($id);

        // Verify enrollment
        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $quiz->course_id)
            ->exists();

        if (!$isEnrolled) {
            abort(403);
        }

        $attempts = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.quizzes.show', compact('quiz', 'attempts'));
    }

    public function start($id)
    {
        $userId = auth()->id();
        $quiz = Quiz::where('is_published', true)->findOrFail($id);

        // Check attempts limit
        $attemptsCount = QuizAttempt::where('quiz_id', $id)
            ->where('user_id', $userId)
            ->count();

        if ($attemptsCount >= $quiz->max_attempts) {
            return redirect()->route('student.quizzes.show', $id)
                ->with('error', __('The maximum number of attempts has been reached.'));
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $userId,
            'started_at' => now(),
            'total_points' => $quiz->questions()->sum('points'),
        ]);

        return redirect()->route('student.quizzes.take', $attempt->id);
    }

    public function take($attemptId)
    {
        $userId = auth()->id();
        $attempt = QuizAttempt::with('quiz.questions.options')->findOrFail($attemptId);

        if ($attempt->user_id !== $userId || $attempt->completed_at !== null) {
            abort(403);
        }

        // Calculate remaining time
        $quiz = $attempt->quiz;
        $remainingSeconds = null;
        if ($quiz->duration_minutes) {
            $endTime = $attempt->started_at->addMinutes($quiz->duration_minutes);
            $remainingSeconds = max(0, $endTime->timestamp - now()->timestamp);
            
            // Auto submit if time expired
            if ($remainingSeconds <= 0) {
                return $this->autoSubmit($attempt);
            }
        }

        return view('student.quizzes.take', compact('attempt', 'remainingSeconds'));
    }

    public function submit(Request $request, $attemptId)
    {
        $userId = auth()->id();
        $attempt = QuizAttempt::with('quiz.questions.options')->findOrFail($attemptId);

        if ($attempt->user_id !== $userId || $attempt->completed_at !== null) {
            abort(403);
        }

        $answers = $request->input('answers', []);
        $questions = $attempt->quiz->questions;

        $earnedPoints = 0;
        $totalPoints = $attempt->total_points;

        foreach ($questions as $q) {
            $selectedOptionId = $answers[$q->id] ?? null;
            $isCorrect = false;

            if ($selectedOptionId) {
                $option = QuizOption::where('question_id', $q->id)->find($selectedOptionId);
                if ($option && $option->is_correct) {
                    $isCorrect = true;
                    $earnedPoints += $q->points;
                }
            }

            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $q->id,
                'option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
            ]);
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : 0;
        $passed = $score >= $attempt->quiz->pass_percentage;

        $attempt->update([
            'score' => $score,
            'earned_points' => $earnedPoints,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        return redirect()->route('student.quizzes.result', $attempt->id);
    }

    public function result($attemptId)
    {
        $userId = auth()->id();
        $attempt = QuizAttempt::with(['quiz.questions.options', 'answers'])->findOrFail($attemptId);

        if ($attempt->user_id !== $userId) {
            abort(403);
        }

        return view('student.quizzes.result', compact('attempt'));
    }

    protected function autoSubmit(QuizAttempt $attempt)
    {
        $attempt->update([
            'score' => 0,
            'earned_points' => 0,
            'passed' => false,
            'completed_at' => now(),
        ]);
        return redirect()->route('student.quizzes.result', $attempt->id)
            ->with('error', __('Time is up! The test was submitted automatically.'));
    }
}