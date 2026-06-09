<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected function getTeacherCourseIds()
    {
        return Course::where('instructor_id', auth()->id())
            ->orWhere('user_id', auth()->id())
            ->pluck('id')
            ->toArray();
    }

    public function index()
    {
        $courseIds = $this->getTeacherCourseIds();
        $modules = Module::whereIn('course_id', $courseIds)->with('course')->orderBy('sort_order')->get();
        return view('teacher.modules.index', compact('modules'));
    }

    public function create()
    {
        $courses = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->get();
        return view('teacher.modules.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($request->course_id, $courseIds)) {
            abort(403);
        }

        Module::create($request->all());

        return redirect()->route('teacher.modules.index')->with('success', __('Module created successfully!'));
    }

    public function edit(Module $module)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($module->course_id, $courseIds)) {
            abort(403);
        }

        $courses = Course::where('instructor_id', auth()->id())->orWhere('user_id', auth()->id())->get();
        return view('teacher.modules.edit', compact('module', 'courses'));
    }

    public function update(Request $request, Module $module)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($module->course_id, $courseIds) || !in_array($request->course_id, $courseIds)) {
            abort(403);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_published' => 'required|boolean',
        ]);

        $module->update($request->all());

        return redirect()->route('teacher.modules.index')->with('success', __('The module has been updated!'));
    }

    public function destroy(Module $module)
    {
        $courseIds = $this->getTeacherCourseIds();
        if (!in_array($module->course_id, $courseIds)) {
            abort(403);
        }

        $module->delete();

        return redirect()->route('teacher.modules.index')->with('success', __('The module has been disabled.'));
    }
}