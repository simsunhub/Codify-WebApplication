<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        $courseIds = Course::where('instructor_id', $teacherId)->orWhere('user_id', $teacherId)->pluck('id');

        $students = User::whereIn('id', function ($query) use ($courseIds) {
                $query->select('user_id')->from('enrollments')->whereIn('course_id', $courseIds);
            })
            ->withCount(['enrollments' => function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            }])
            ->get();

        return view('teacher.students.index', compact('students'));
    }

    public function show($id)
    {
        $student = User::findOrFail($id);
        $teacherId = auth()->id();
        $courseIds = Course::where('instructor_id', $teacherId)->orWhere('user_id', $teacherId)->pluck('id');

        $enrollments = Enrollment::where('user_id', $student->id)
            ->whereIn('course_id', $courseIds)
            ->with('course.lessons')
            ->get();

        return view('teacher.students.show', compact('student', 'enrollments'));
    }
}
