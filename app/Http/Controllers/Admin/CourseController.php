<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'user'])
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $instructors = User::query()
            ->where('role', 'instructor')
            ->orderBy('name')
            ->get();

        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCourse($request);

        $course = new Course();
        $course->title = $validated['title'];
        $course->category_id = $validated['category_id'];
        $course->instructor_id = $validated['instructor_id'];
        $course->description = $validated['description'] ?? null;
        $course->price = $validated['price'];
        $course->status = $validated['status'] ?? 'draft';
        $course->slug = $this->generateUniqueSlug($validated['title']);

        if ($request->hasFile('image')) {
            $course->image_path = $request->file('image')->store('courses', 'public');
        }

        $course->save();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $instructors = User::query()
            ->where('role', 'instructor')
            ->orderBy('name')
            ->get();

        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $this->validateCourse($request);

        $course->title = $validated['title'];
        $course->category_id = $validated['category_id'];
        $course->instructor_id = $validated['instructor_id'];
        $course->description = $validated['description'] ?? null;
        $course->price = $validated['price'];
        $course->status = $validated['status'] ?? 'draft';
        $course->slug = $this->generateUniqueSlug($validated['title'], $course->id);

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            $course->image_path = $request->file('image')->store('courses', 'public');
        }

        $course->save();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->image_path) {
            Storage::disk('public')->delete($course->image_path);
        }

        $course->loadMissing('lessons');

        foreach ($course->lessons as $lesson) {
            if ($lesson->video_url) {
                Storage::disk('public')->delete($lesson->video_url);
            }
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'instructor_id' => ['required', 'integer', 'exists:users,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
        ]);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreCourseId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'course';
        $slug = $baseSlug;
        $suffix = 1;

        while (Course::query()
            ->when($ignoreCourseId, fn ($query) => $query->where('id', '!=', $ignoreCourseId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
