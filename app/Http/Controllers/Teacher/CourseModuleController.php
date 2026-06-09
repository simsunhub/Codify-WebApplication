<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModuleSetting;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    /**
     * Show modules settings for a specific course.
     */
    public function index($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Security check: must be the course instructor
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Get active settings or build defaults
        $moduleNames = ['assignments', 'quizzes', 'practice'];
        $settings = [];

        foreach ($moduleNames as $moduleName) {
            $setting = CourseModuleSetting::firstOrCreate(
                ['course_id' => $course->id, 'module_name' => $moduleName],
                ['is_enabled' => true]
            );
            $settings[$moduleName] = $setting;
        }

        return view('teacher.courses.modules', compact('course', 'settings'));
    }

    /**
     * Update modules settings override for a specific course.
     */
    public function update(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Security check
        if ($course->instructor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'modules' => 'required|array',
            'modules.*' => 'required|in:0,1',
        ]);

        foreach ($request->input('modules') as $moduleName => $isEnabled) {
            if (in_array($moduleName, ['assignments', 'quizzes', 'practice'])) {
                CourseModuleSetting::updateOrCreate(
                    ['course_id' => $course->id, 'module_name' => $moduleName],
                    ['is_enabled' => (bool) $isEnabled]
                );
            }
        }

        return redirect()->route('teacher.courses.modules.index', $course->id)
            ->with('success', __('messages.admin.modules.course_override_success'));
    }
}
