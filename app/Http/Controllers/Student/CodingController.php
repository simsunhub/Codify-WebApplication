<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CodingProblem;
use App\Models\CodingSubmission;
use App\Models\ProgrammingLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CodingController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $problems = CodingProblem::published()->orderBy('sort_order')->get();
        
        $submissions = CodingSubmission::where('user_id', $userId)
            ->with(['problem', 'language'])
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        $totalSolved = CodingSubmission::where('user_id', $userId)
            ->where('status', 'accepted')
            ->distinct('problem_id')
            ->count('problem_id');

        $totalSubmissions = CodingSubmission::where('user_id', $userId)->count();
        $acceptedSubmissions = CodingSubmission::where('user_id', $userId)->where('status', 'accepted')->count();
        $successRate = $totalSubmissions > 0 ? round(($acceptedSubmissions / $totalSubmissions) * 100, 1) : 0;

        $langs = ProgrammingLanguage::active()->get();

        $langStats = CodingSubmission::where('user_id', $userId)
            ->select('language_id', DB::raw('count(*) as count'))
            ->groupBy('language_id')
            ->with('language')
            ->get();

        return view('student.coding.index', compact(
            'problems',
            'submissions',
            'totalSolved',
            'successRate',
            'langStats',
            'langs'
        ));
    }

    public function show($slug)
    {
        $problem = CodingProblem::where('slug', $slug)->published()->firstOrFail();
        $languages = ProgrammingLanguage::active()->get();
        
        $submissions = CodingSubmission::where('user_id', auth()->id())
            ->where('problem_id', $problem->id)
            ->with(['problem', 'language'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('student.coding.show', compact('problem', 'languages', 'submissions'));
    }

    public function submit(Request $request, $slug)
    {
        $request->validate([
            'language_id' => 'required|exists:programming_languages,id',
            'code' => 'required|string',
        ]);

        $problem = CodingProblem::where('slug', $slug)->published()->firstOrFail();
        $language = ProgrammingLanguage::findOrFail($request->language_id);
        
        // Record submission (mock execution/acceptance)
        $submission = CodingSubmission::create([
            'user_id' => auth()->id(),
            'problem_id' => $problem->id,
            'language_id' => $language->id,
            'code' => $request->code,
            'status' => 'accepted',
            'runtime_ms' => rand(10, 150),
            'memory_kb' => rand(1000, 5000),
            'submitted_at' => now(),
        ]);

        $problem->increment('attempt_count');
        $problem->increment('solved_count');

        return response()->json([
            'success' => true,
            'status' => $submission->status,
            'runtime_ms' => $submission->runtime_ms,
            'memory_kb' => $submission->memory_kb,
            'test_results' => [
                ['status' => 'passed', 'input' => 'test input', 'expected' => 'expected output']
            ]
        ]);
    }

    public function run(Request $request, $slug)
    {
        $request->validate([
            'language_id' => 'required|exists:programming_languages,id',
            'code' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'status' => 'passed',
            'output' => "Executed successfully.\nAll tests passed."
        ]);
    }
}