<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Teacher/Instructor Course Management Controller
 *
 * Handles full CRUD for courses owned by the authenticated instructor.
 * Enforces ownership checks on edit/update/destroy to prevent
 * horizontal privilege escalation.
 */
class CourseController extends Controller
{
    /**
     * Display a paginated list of the instructor's courses.
     */
    public function index()
    {
        $courses = Course::with('category')
            ->withCount(['enrollments', 'lessons', 'reviews'])
            ->where('instructor_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Show the form to create a new course.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('teacher.courses.create', compact('categories'));
    }

    /**
     * Validate and store a new course.
     */
    public function store(Request $request)
    {
        // ── Strict validation rules ─────────────────────────────
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price'       => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'level'       => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'status'      => ['nullable', Rule::in(['draft', 'published'])],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // ── Prepare data ────────────────────────────────────────
        $data = [
            'title'         => $validated['title'],
            'slug'          => Str::slug($validated['title']) . '-' . uniqid(),
            'category_id'   => $validated['category_id'],
            'description'   => $validated['description'] ?? null,
            'price'         => $validated['price'],
            'level'         => $validated['level'] ?? 'beginner',
            'status'        => $validated['status'] ?? 'draft',
            'instructor_id' => auth()->id(),
        ];

        // ── Handle image upload ─────────────────────────────────
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        Course::create($data);

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Course created successfully!');
    }

    /**
     * Show the course detail page (public preview).
     */
    public function show(Course $course)
    {
        $this->authorizeOwnership($course);

        return redirect()->route('course.show', $course->slug);
    }

    /**
     * Show the form to edit a course.
     */
    public function edit(Course $course)
    {
        $this->authorizeOwnership($course);

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('teacher.courses.edit', compact('course', 'categories'));
    }

    /**
     * Validate and update a course.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorizeOwnership($course);

        // ── Strict validation rules ─────────────────────────────
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price'       => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'level'       => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'status'      => ['nullable', Rule::in(['draft', 'published'])],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // ── Prepare data ────────────────────────────────────────
        $data = [
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']) . '-' . uniqid(),
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? $course->description,
            'price'       => $validated['price'],
            'level'       => $validated['level'] ?? $course->level,
            'status'      => $validated['status'] ?? $course->status,
        ];

        // ── Handle image replacement ─────────────────────────────
        if ($request->hasFile('image')) {
            // Delete old thumbnail if it exists
            $oldImage = $course->image_path ?? $course->image;
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            $data['image_path'] = $request->file('image')->store('courses', 'public');
        }

        $course->update($data);

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Delete a course and all associated media.
     */
    public function destroy(Course $course)
    {
        $this->authorizeOwnership($course);

        // Delete course thumbnail
        $image = $course->image_path ?? $course->image;
        if ($image) {
            Storage::disk('public')->delete($image);
        }

        // Delete lesson video files (if stored locally)
        foreach ($course->lessons as $lesson) {
            if ($lesson->video_url && !str_starts_with($lesson->video_url, 'http')) {
                Storage::disk('public')->delete($lesson->video_url);
            }
        }

        $course->delete();

        return redirect()
            ->route('teacher.courses.index')
            ->with('success', 'Course permanently deleted!');
    }

    /**
     * Ensure the authenticated user owns the course.
     * Uses both instructor_id (new) and falls back to user_id (legacy) checks.
     */
    private function authorizeOwnership(Course $course): void
    {
        $ownerId = $course->instructor_id ?? $course->user_id;

        if ((int) $ownerId !== (int) auth()->id()) {
            abort(403, 'You do not own this course.');
        }
    }
}
