<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentQuizTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $course;
    protected $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create(['role' => 'student']);

        $category = Category::create([
            'name' => 'IT & Soft',
            'slug' => 'it-soft',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'user_id' => User::factory()->create(['role' => 'teacher'])->id,
            'category_id' => $category->id,
            'title' => 'Laravel Masterclass',
            'slug' => 'laravel-masterclass',
            'price' => 0,
            'level' => 'intermediate',
            'status' => 'published',
        ]);

        $this->quiz = Quiz::create([
            'course_id' => $this->course->id,
            'title' => 'Laravel Basics Quiz',
            'pass_percentage' => 80,
            'max_attempts' => 3,
            'sort_order' => 1,
            'is_published' => true,
        ]);
    }

    public function test_student_cannot_view_quiz_if_not_enrolled()
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.quizzes.show', $this->quiz->id));

        $response->assertStatus(403);
    }

    public function test_student_can_view_quiz_if_enrolled()
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.quizzes.show', $this->quiz->id));

        $response->assertStatus(200)
            ->assertSee('Laravel Basics Quiz');
    }

    public function test_student_can_start_quiz()
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.quizzes.start', $this->quiz->id));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
        ]);
    }

    public function test_student_cannot_start_quiz_if_already_passed()
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        // Create a passed attempt
        QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'started_at' => now(),
            'completed_at' => now(),
            'score' => 100,
            'passed' => true,
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('student.quizzes.start', $this->quiz->id));

        $response->assertRedirect(route('student.quizzes.show', $this->quiz->id));
        $response->assertSessionHas('error', __('You have already passed this quiz.'));
    }

    public function test_student_cannot_start_quiz_if_attempts_limit_reached()
    {
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        // Create 3 failed attempts
        for ($i = 0; $i < 3; $i++) {
            QuizAttempt::create([
                'quiz_id' => $this->quiz->id,
                'user_id' => $this->student->id,
                'started_at' => now(),
                'completed_at' => now(),
                'score' => 20,
                'passed' => false,
            ]);
        }

        $response = $this->actingAs($this->student)
            ->post(route('student.quizzes.start', $this->quiz->id));

        $response->assertRedirect(route('student.quizzes.show', $this->quiz->id));
        $response->assertSessionHas('error', __('The maximum number of attempts has been reached.'));
    }
}
