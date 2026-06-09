<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Get courses enrolled by student
        $courseIds = Enrollment::where('user_id', $userId)->pluck('course_id');

        // Fetch assignments for these courses
        $assignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with(['course', 'submissions' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('student.assignments.index', compact('assignments'));
    }

    public function show($id)
    {
        $userId = auth()->id();
        $assignment = Assignment::where('is_published', true)->findOrFail($id);

        // Verify enrollment
        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $assignment->course_id)
            ->exists();

        if (!$isEnrolled) {
            abort(403, __('You are not enrolled in this course.'));
        }

        $submission = AssignmentSubmission::where('assignment_id', $id)
            ->where('user_id', $userId)
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, $id)
    {
        $userId = auth()->id();
        $assignment = Assignment::where('is_published', true)->findOrFail($id);

        // Verify enrollment
        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $assignment->course_id)
            ->exists();

        if (!$isEnrolled) {
            abort(403);
        }

        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:' . ($assignment->max_file_size * 1024) . '|mimes:' . str_replace(' ', '', $assignment->allowed_extensions),
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            // Store file inside public/storage or secure location
            $filePath = $file->store('submissions', 'public');
        }

        // Create or update submission
        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $userId],
            [
                'content' => $request->content,
                'file_path' => $filePath ?? $request->input('old_file_path'),
                'file_name' => $fileName ?? $request->input('old_file_name'),
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('student.assignments.show', $assignment->id)
            ->with('success', __('Task uploaded successfully!'));
    }
}