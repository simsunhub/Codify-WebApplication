<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\LmsModule;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Quiz;
use Symfony\Component\HttpFoundation\Response;

class LmsModuleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleName
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduleName): Response
    {
        $user = auth()->user();

        // 1. Determine course context if any from route parameters
        $course = null;

        // Try route parameter 'course' (could be model or ID or slug)
        if ($request->route('course')) {
            $courseVal = $request->route('course');
            if ($courseVal instanceof Course) {
                $course = $courseVal;
            } elseif (is_numeric($courseVal)) {
                $course = Course::find($courseVal);
            } else {
                $course = Course::where('slug', $courseVal)->first();
            }
        }
        
        // Try route parameter 'slug' (which might be the course slug)
        if (!$course && $request->route('slug')) {
            $slugVal = $request->route('slug');
            $course = Course::where('slug', $slugVal)->first();
        }

        // Try route parameter 'assignment'
        if (!$course && $request->route('assignment')) {
            $assignmentVal = $request->route('assignment');
            if ($assignmentVal instanceof Assignment) {
                $course = $assignmentVal->course;
            } elseif (is_numeric($assignmentVal)) {
                $assignment = Assignment::find($assignmentVal);
                $course = $assignment ? $assignment->course : null;
            }
        }

        // Try route parameter 'quiz'
        if (!$course && $request->route('quiz')) {
            $quizVal = $request->route('quiz');
            if ($quizVal instanceof Quiz) {
                $course = $quizVal->course;
            } elseif (is_numeric($quizVal)) {
                $quiz = Quiz::find($quizVal);
                $course = $quiz ? $quiz->course : null;
            }
        }

        // 2. Perform the LmsModule checks
        if (!LmsModule::isVisible($moduleName, $course)) {
            $translatedName = __('messages.dash.' . $moduleName);
            if ($translatedName === 'messages.dash.' . $moduleName && $moduleName === 'my_learning') {
                $translatedName = __('messages.learning.title');
            }
            return redirect()->route('dashboard')->with('error', __('messages.admin.modules.disabled_error', ['module' => $translatedName]));
        }

        if (LmsModule::isLocked($moduleName, $user)) {
            $translatedName = __('messages.dash.' . $moduleName);
            if ($translatedName === 'messages.dash.' . $moduleName && $moduleName === 'my_learning') {
                $translatedName = __('messages.learning.title');
            }
            return redirect()->route('dashboard')->with('error', __('messages.admin.modules.premium_error', ['module' => $translatedName]));
        }

        return $next($request);
    }
}
