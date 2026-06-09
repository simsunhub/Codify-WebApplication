<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\CodingProblem;
use App\Models\CodingTestCase;
use App\Models\ProgrammingLanguage;
use App\Models\CodingSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CodingController extends Controller
{
    public function index()
    {
        $problems = CodingProblem::where('created_by', auth()->id())->orderBy('sort_order')->get();
        return view('teacher.coding.index', compact('problems'));
    }

    public function create()
    {
        $languages = ProgrammingLanguage::active()->get();
        return view('teacher.coding.create', compact('languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'nullable|string',
            'starter_code' => 'required|array',
        ]);

        $starterCode = [];
        foreach ($request->starter_code as $langSlug => $code) {
            if (!empty($code)) {
                $starterCode[$langSlug] = $code;
            }
        }

        CodingProblem::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'category' => $request->category,
            'constraints' => $request->constraints,
            'hints' => $request->hints ? array_filter($request->hints) : [],
            'starter_code' => $starterCode,
            'created_by' => auth()->id(),
            'is_published' => true,
        ]);

        return redirect()->route('teacher.coding.index')->with('success', __('Issue created successfully!'));
    }

    public function edit($id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);
        $languages = ProgrammingLanguage::active()->get();
        return view('teacher.coding.edit', compact('problem', 'languages'));
    }

    public function update(Request $request, $id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'nullable|string',
            'starter_code' => 'required|array',
        ]);

        $starterCode = [];
        foreach ($request->starter_code as $langSlug => $code) {
            if (!empty($code)) {
                $starterCode[$langSlug] = $code;
            }
        }

        $problem->update([
            'title' => $request->title,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'category' => $request->category,
            'constraints' => $request->constraints,
            'hints' => $request->hints ? array_filter($request->hints) : [],
            'starter_code' => $starterCode,
        ]);

        return redirect()->route('teacher.coding.index')->with('success', __('Issue updated!'));
    }

    public function destroy($id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);
        $problem->delete();

        return redirect()->route('teacher.coding.index')->with('success', __('Issue deleted.'));
    }

    public function testCases($id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);
        $testCases = CodingTestCase::where('problem_id', $problem->id)->get();
        return view('teacher.coding.test_cases', compact('problem', 'testCases'));
    }

    public function storeTestCase(Request $request, $id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);

        $request->validate([
            'input' => 'required|string',
            'expected_output' => 'required|string',
            'is_sample' => 'required|boolean',
        ]);

        CodingTestCase::create([
            'problem_id' => $problem->id,
            'input' => $request->input,
            'expected_output' => $request->expected_output,
            'is_sample' => $request->is_sample,
            'sort_order' => CodingTestCase::where('problem_id', $problem->id)->count() + 1,
        ]);

        return redirect()->route('teacher.coding.test-cases', $problem->id)->with('success', __('Error 500 (Server Error)!!1500.That’s an error.There was an error. Please try again later.That’s all we know.'));
    }

    public function submissions($id)
    {
        $problem = CodingProblem::where('created_by', auth()->id())->findOrFail($id);
        $submissions = CodingSubmission::where('problem_id', $problem->id)->with(['user', 'language'])->orderBy('submitted_at', 'desc')->get();
        return view('teacher.coding.submissions', compact('problem', 'submissions'));
    }
}