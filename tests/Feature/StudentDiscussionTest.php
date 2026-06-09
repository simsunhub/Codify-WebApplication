<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDiscussionTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_create_discussion(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $category = Category::create([
            'name' => 'IT',
            'slug' => 'it'
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test Description',
            'price' => 0.00,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        // Enroll student
        $course->enrollments()->create([
            'user_id' => $student->id,
            'price_paid' => 0,
            'status' => 'active'
        ]);

        $response = $this->actingAs($student)->postJson(route('student.discussions.store', $course->id), [
            'title' => 'How to write PHP tests?',
            'body' => 'I am struggling with PHPUnit feature testing. Any tips?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'discussion' => [
                'id',
                'title',
                'body',
                'course_id',
                'user_id',
                'user'
            ]
        ]);

        $this->assertDatabaseHas('discussions', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'title' => 'How to write PHP tests?',
            'body' => 'I am struggling with PHPUnit feature testing. Any tips?',
        ]);
    }

    public function test_student_can_reply_to_discussion(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $category = Category::create([
            'name' => 'IT',
            'slug' => 'it'
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test Description',
            'price' => 0.00,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $discussion = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'title' => 'My Question',
            'body' => 'Details',
        ]);

        $response = $this->actingAs($student)->postJson(route('student.discussions.reply', $discussion->id), [
            'body' => 'Here is a self-reply tip.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('is_answered', false); // Student replying to own doesn't mark it answered

        $this->assertDatabaseHas('discussion_replies', [
            'discussion_id' => $discussion->id,
            'user_id' => $student->id,
            'body' => 'Here is a self-reply tip.',
        ]);

        $this->assertEquals(1, $discussion->fresh()->replies_count);
    }

    public function test_instructor_reply_marks_discussion_as_answered(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $category = Category::create([
            'name' => 'IT',
            'slug' => 'it'
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test Description',
            'price' => 0.00,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $discussion = Discussion::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'title' => 'My Question',
            'body' => 'Details',
        ]);

        $response = $this->actingAs($instructor)->postJson(route('student.discussions.reply', $discussion->id), [
            'body' => 'This is the official answer.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('is_answered', true); // Instructor replying marks it answered

        $this->assertDatabaseHas('discussion_replies', [
            'discussion_id' => $discussion->id,
            'user_id' => $instructor->id,
            'body' => 'This is the official answer.',
            'is_answer' => true,
        ]);

        $this->assertTrue($discussion->fresh()->is_answered);
    }
}
