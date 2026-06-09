<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\StudentList;
use App\Models\LessonProgress;
use App\Models\CodingProblem;
use App\Models\ProgrammingLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentListAndProgressTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $teacher;
    protected $category;
    protected $course;
    protected $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => 'student', 'name' => 'John Student']);
        $this->teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Jane Teacher']);

        $this->category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Intro to Web Dev',
            'slug' => 'intro-to-web-dev',
            'description' => 'A comprehensive introduction to web development.',
            'price' => 0,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $this->lesson = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'HTML Basics',
            'content_text' => 'Learn about HTML tags.',
            'video_url' => 'https://youtube.com/watch?v=html1',
            'sort_order' => 1,
        ]);
    }

    public function test_student_can_toggle_lesson_progress()
    {
        // Complete the lesson via POST
        $response = $this->actingAs($this->student)
            ->post(route('lesson.complete', $this->lesson->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => true,
        ]);

        // Uncomplete the lesson via POST
        $response = $this->actingAs($this->student)
            ->post(route('lesson.uncomplete', $this->lesson->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => false,
        ]);
    }

    public function test_student_can_toggle_lesson_progress_via_ajax()
    {
        // Complete lesson via AJAX
        $response = $this->actingAs($this->student)
            ->postJson(route('lesson.complete', $this->lesson->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'completed' => true,
                'completedCount' => 1,
                'totalLessons' => 1,
                'progress' => 100,
            ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => true,
        ]);

        // Uncomplete lesson via AJAX
        $response = $this->actingAs($this->student)
            ->postJson(route('lesson.uncomplete', $this->lesson->id));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'completed' => false,
                'completedCount' => 0,
                'totalLessons' => 1,
                'progress' => 0,
            ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => false,
        ]);
    }

    public function test_student_can_toggle_course_in_playlist()
    {
        // Add to playlist
        $response = $this->actingAs($this->student)
            ->post(route('student.playlist.toggle', $this->course->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'playlist',
        ]);

        // Access playlist page
        $response = $this->actingAs($this->student)
            ->get(route('student.playlist'));

        $response->assertStatus(200)
            ->assertSee($this->course->title);

        // Remove from playlist
        $response = $this->actingAs($this->student)
            ->post(route('student.playlist.toggle', $this->course->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'playlist',
        ]);
    }

    public function test_student_can_toggle_course_in_watch_later()
    {
        // Add to watch later
        $response = $this->actingAs($this->student)
            ->post(route('student.watch-later.toggle', $this->course->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'watch_later',
        ]);

        // Access watch later page
        $response = $this->actingAs($this->student)
            ->get(route('student.watch-later'));

        $response->assertStatus(200)
            ->assertSee($this->course->title);

        // Remove from watch later
        $response = $this->actingAs($this->student)
            ->post(route('student.watch-later.toggle', $this->course->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'watch_later',
        ]);
    }

    public function test_student_can_toggle_lesson_progress_via_new_ajax_route()
    {
        // Toggle complete
        $response = $this->actingAs($this->student)
            ->postJson(route('lessons.complete', $this->lesson->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'completed' => true,
            ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => true,
        ]);

        // Toggle again (uncomplete)
        $response = $this->actingAs($this->student)
            ->postJson(route('lessons.complete', $this->lesson->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'completed' => false,
            ]);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'is_completed' => false,
        ]);
    }

    public function test_student_can_toggle_course_list_via_new_ajax_route()
    {
        // Add to playlist
        $response = $this->actingAs($this->student)
            ->postJson(route('courses.toggle-list', $this->course->id), ['type' => 'playlist']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'added' => true,
            ]);

        $this->assertDatabaseHas('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'playlist',
        ]);

        // Remove from playlist
        $response = $this->actingAs($this->student)
            ->postJson(route('courses.toggle-list', $this->course->id), ['type' => 'playlist']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'added' => false,
            ]);

        $this->assertDatabaseMissing('student_lists', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'list_type' => 'playlist',
        ]);
    }

    public function test_student_can_rate_lesson_via_ajax_route()
    {
        // Make sure student is enrolled to course (so they can review)
        \App\Models\Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->postJson(route('lessons.review', $this->lesson->id), ['rating' => 5]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'rating' => 5,
            ]);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'lesson_id' => $this->lesson->id,
            'rating' => 5,
        ]);
    }

    public function test_student_can_comment_on_lesson_via_ajax_route()
    {
        $response = $this->actingAs($this->student)
            ->postJson(route('lessons.comment', $this->lesson->id), ['content' => 'Great lesson!']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'content' => 'Great lesson!',
        ]);
    }

    public function test_student_can_view_coding_problem_workspace()
    {
        // Create an active language
        $lang = ProgrammingLanguage::create([
            'name' => 'PHP',
            'slug' => 'php',
            'version' => '8.2',
            'is_active' => true,
            'judge_id' => 68,
            'monaco_language' => 'php',
            'file_extension' => '.php',
            'sort_order' => 1,
        ]);

        // Create a coding problem
        $problem = CodingProblem::create([
            'title' => 'Two Sum',
            'slug' => 'two-sum',
            'description' => '<p>Solve Two Sum problem</p>',
            'difficulty' => 'easy',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.coding.show', $problem->slug));

        $response->assertOk();
        $response->assertSee('Two Sum');
    }
}
