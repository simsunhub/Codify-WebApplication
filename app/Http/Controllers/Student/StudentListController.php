<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentList;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentListController extends Controller
{
    public function toggleList(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $course = Course::findOrFail($id);
        $type = $request->input('type');

        if (!in_array($type, ['playlist', 'watch_later'])) {
            return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
        }

        $item = StudentList::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('list_type', $type)
            ->first();

        if ($item) {
            $item->delete();
            return response()->json([
                'success' => true,
                'added' => false,
                'message' => 'Removed'
            ]);
        } else {
            StudentList::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'list_type' => $type,
            ]);
            return response()->json([
                'success' => true,
                'added' => true,
                'message' => 'Added'
            ]);
        }
    }
}
