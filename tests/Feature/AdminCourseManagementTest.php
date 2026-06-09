<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->instructor = User::factory()->create(['role' => 'instructor']);
        $this->category = Category::create([
            'name' => 'Programming',
            'slug' => 'programming',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_course_with_status_and_instructor(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.courses.store'), [
            'title' => 'Laravel Automation',
            'category_id' => $this->category->id,
            'instructor_id' => $this->instructor->id,
            'price' => 49.99,
            'description' => 'Automation workflows for modern teams.',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', [
            'title' => 'Laravel Automation',
            'category_id' => $this->category->id,
            'instructor_id' => $this->instructor->id,
            'status' => 'published',
            'price' => 49.99,
        ]);
    }

    public function test_admin_can_update_existing_course(): void
    {
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Old Title',
            'slug' => 'old-title',
            'description' => 'Legacy description',
            'price' => 20,
            'status' => 'draft',
            'level' => 'beginner',
        ]);

        $otherInstructor = User::factory()->create(['role' => 'instructor']);

        $response = $this->actingAs($this->admin)->put(route('admin.courses.update', $course), [
            'title' => 'Updated Title',
            'category_id' => $this->category->id,
            'instructor_id' => $otherInstructor->id,
            'price' => 79.5,
            'description' => 'Updated course description',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Title',
            'instructor_id' => $otherInstructor->id,
            'status' => 'published',
            'price' => 79.5,
        ]);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'description' => 'Remove this record',
            'price' => 0,
            'status' => 'draft',
            'level' => 'beginner',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courses.destroy', $course));

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}