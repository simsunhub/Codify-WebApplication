<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsModule extends Model
{
    protected $fillable = [
        'module_name',
        'is_enabled',
        'accessible_by',
        'max_limit',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'max_limit' => 'integer',
    ];

    /**
     * Determine if a module is visible (globally enabled and course enabled).
     *
     * @param string $moduleName
     * @param mixed $course
     * @return bool
     */
    public static function isVisible(string $moduleName, $course = null): bool
    {
        $module = self::where('module_name', $moduleName)->first();
        
        if (!$module || !$module->is_enabled || $module->accessible_by === 'disabled') {
            return false;
        }

        // Check course overrides if applicable
        if ($course && in_array($moduleName, ['assignments', 'quizzes', 'practice'])) {
            $courseSetting = CourseModuleSetting::where('course_id', $course instanceof Course ? $course->id : $course)
                ->where('module_name', $moduleName)
                ->first();
            
            if ($courseSetting && !$courseSetting->is_enabled) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if a module is locked for the given user.
     *
     * @param string $moduleName
     * @param mixed $user
     * @return bool
     */
    public static function isLocked(string $moduleName, $user = null): bool
    {
        $module = self::where('module_name', $moduleName)->first();
        
        if (!$module || !$module->is_enabled || $module->accessible_by !== 'premium_only') {
            return false;
        }

        if (!$user) {
            return true;
        }

        // Admin and instructors bypass premium locks
        if ($user->isAdmin() || $user->isInstructor()) {
            return false;
        }

        return !$user->is_premium;
    }

    /**
     * Check both visibility and access for routes.
     *
     * @param string $moduleName
     * @param mixed $user
     * @param mixed $course
     * @return bool
     */
    public static function isAccessible(string $moduleName, $user = null, $course = null): bool
    {
        if (!self::isVisible($moduleName, $course)) {
            return false;
        }

        if (self::isLocked($moduleName, $user)) {
            return false;
        }

        return true;
    }
}
