<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    protected function getTeacherCourseIds()
    {
        return Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->pluck('id')->toArray();
    }

    public function index()
    {
        $courseIds = $this->getTeacherCourseIds();
        $assignments = Assignment::whereIn('course_id', $courseIds)->with(['course', 'module'])->orderBy('sort_order')->get();
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->get();
        $modules = Module::whereIn('course_id', $courses->pluck('id'))->get();
        return view('teacher.assignments.create', compact('courses', 'modules'));
    }

    public function store(Request $request)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($request->course_id, $courseIds)) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'nullable|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:1',
            'sort_order' => 'required|integer',
        ]);

        Assignment::create($request->all());

        return redirect()->route('teacher.assignments.index')->with('success', __('Task created!'));
    }

    public function edit(Assignment $assignment)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($assignment->course_id, $courseIds)) {
            abort(403);
        }

        $courses = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->get();
        $modules = Module::whereIn('course_id', $courses->pluck('id'))->get();
        return view('teacher.assignments.edit', compact('assignment', 'courses', 'modules'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($assignment->course_id, $courseIds) || !in_array($request->course_id, $courseIds)) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'nullable|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:1',
            'sort_order' => 'required|integer',
            'is_published' => 'required|boolean',
        ]);

        $assignment->update($request->all());

        return redirect()->route('teacher.assignments.index')->with('success', __('Task updated!'));
    }

    public function destroy(Assignment $assignment)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($assignment->course_id, $courseIds)) {
            abort(403);
        }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')->with('success', __('Task deleted.'));
    }

    public function submissions(Assignment $assignment)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($assignment->course_id, $courseIds)) {
            abort(403);
        }

        $submissions = AssignmentSubmission::where('assignment_id', $assignment->id)->with('user')->get();
        return view('teacher.assignments.submissions', compact('assignment', 'submissions'));
    }

    public function grade(Request $request, $submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);
        $assignment = $submission->assignment;

        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($assignment->course_id, $courseIds)) {
            abort(403);
        }

        $request->validate([
            'score' => 'required|integer|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        // Send student notification
        \App\Models\Notification::create([
            'user_id' => $submission->user_id,
            'type' => 'comment',
            'title' => __('Your assignment has been evaluated'),
            'body' => __("Your assignment for \"{$assignment->title}\" has been graded. Your score: {$request->score} / {$assignment->max_score}"),
            'url' => route('student.assignments.show', $assignment->id),
            'is_read' => false,
        ]);

        return redirect()->route('teacher.assignments.submissions', $assignment->id)->with('success', __('The task is appreciated!'));
    }
}