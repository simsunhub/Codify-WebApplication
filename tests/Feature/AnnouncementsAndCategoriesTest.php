<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementsAndCategoriesTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;
    protected $category;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        $this->teacher = User::factory()->create(['role' => 'instructor', 'name' => 'Teacher User']);
        $this->student = User::factory()->create(['role' => 'student', 'name' => 'Student User']);

        $this->category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Sample Course',
            'slug' => 'sample-course',
            'description' => 'A course description.',
            'price' => 10,
            'level' => 'intermediate',
            'status' => 'published',
        ]);
    }

    public function test_teacher_cannot_access_category_management()
    {
        // Try index
        $response = $this->actingAs($this->teacher)
            ->get(route('admin.categories.index'));
        $response->assertRedirect(route('home'));

        // Try store
        $response = $this->actingAs($this->teacher)
            ->post(route('admin.categories.store'), [
                'name' => 'Teacher Created',
                'slug' => 'teacher-created',
            ]);
        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('categories', ['name' => 'Teacher Created']);
    }

    public function test_admin_can_access_category_management()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Admin Created',
                'slug' => 'admin-created',
                'is_active' => true,
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Admin Created']);
    }

    public function test_admin_cannot_delete_category_with_courses()
    {
        $this->assertGreaterThan(0, $this->category->courses()->count());

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $this->category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $this->category->id]);
    }

    public function test_admin_can_delete_empty_category()
    {
        $emptyCategory = Category::create([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $emptyCategory));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $emptyCategory->id]);
    }

    public function test_admin_can_update_category()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $this->category), [
                'name' => 'Updated Tech',
                'slug' => 'updated-tech',
                'icon' => 'fas fa-code',
                'description' => 'Updated description',
                'is_active' => true,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'id' => $this->category->id,
            'name' => 'Updated Tech',
            'slug' => 'updated-tech',
            'icon' => 'fas fa-code',
        ]);
    }

    public function test_admin_can_publish_role_based_announcements()
    {
        // Global announcement for everyone
        $response = $this->actingAs($this->admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Global News',
                'content' => 'Everyone read this',
                'target_role' => 'all',
                'is_active' => 1,
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'title' => 'Global News',
            'target_role' => 'all',
            'user_id' => $this->admin->id,
        ]);

        // Announcement for teachers only
        $response = $this->actingAs($this->admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Teacher Only News',
                'content' => 'Teachers read this',
                'target_role' => 'teacher_only',
                'is_active' => 1,
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'title' => 'Teacher Only News',
            'target_role' => 'teacher_only',
        ]);
    }

    public function test_teacher_can_publish_course_students_announcements()
    {
        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.announcements.store'), [
                'title' => 'Course Update',
                'content' => 'Students of this course only',
                'course_id' => $this->course->id,
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('announcements', [
            'title' => 'Course Update',
            'target_role' => 'course_students',
            'course_id' => $this->course->id,
            'user_id' => $this->teacher->id,
        ]);
    }

    public function test_dashboards_correctly_filter_announcements()
    {
        // 1. Create various announcements
        // Global
        Announcement::create([
            'title' => 'All Users Ann',
            'content' => 'Content All',
            'target_role' => 'all',
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);
        // Student only
        Announcement::create([
            'title' => 'Students Ann',
            'content' => 'Content Students',
            'target_role' => 'student_only',
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);
        // Teacher only
        Announcement::create([
            'title' => 'Teachers Ann',
            'content' => 'Content Teachers',
            'target_role' => 'teacher_only',
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);
        // Course specific
        $annCourse = Announcement::create([
            'title' => 'Course Specific Ann',
            'content' => 'Content Course',
            'target_role' => 'course_students',
            'course_id' => $this->course->id,
            'user_id' => $this->teacher->id,
            'is_active' => true,
        ]);

        // 2. Check Teacher Dashboard (should see: "All Users Ann", "Teachers Ann", NOT "Students Ann", NOT "Course Specific Ann")
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('All Users Ann');
        $response->assertSee('Teachers Ann');
        $response->assertDontSee('Students Ann');
        $response->assertDontSee('Course Specific Ann');

        // 3. Check Student Dashboard BEFORE enrollment (should see: "All Users Ann", "Students Ann", NOT "Course Specific Ann")
        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('All Users Ann');
        $response->assertSee('Students Ann');
        $response->assertDontSee('Teachers Ann');
        $response->assertDontSee('Course Specific Ann');

        // Enroll student to the course
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        // 4. Check Student Dashboard AFTER enrollment (should see: "All Users Ann", "Students Ann", AND "Course Specific Ann")
        $response = $this->actingAs($this->student)
            ->get(route('student.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('All Users Ann');
        $response->assertSee('Students Ann');
        $response->assertSee('Course Specific Ann');
        $response->assertDontSee('Teachers Ann');
    }
}
