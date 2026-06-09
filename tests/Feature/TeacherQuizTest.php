<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherQuizTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $student;
    protected $category;
    protected $course;
    protected $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->student = User::factory()->create(['role' => 'student']);

        $this->category = Category::create([
            'name' => 'IT & Soft',
            'slug' => 'it-soft',
            'is_active' => true,
        ]);

        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
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
        ]);
    }

    public function test_teacher_can_access_quiz_questions_page()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.quizzes.questions', $this->quiz->id));

        $response->assertStatus(200)
            ->assertSee('Laravel Basics Quiz');
    }

    public function test_teacher_can_store_quiz_question()
    {
        $data = [
            'question' => 'What is Eloquent?',
            'points' => 10,
            'explanation' => 'Eloquent is an ORM.',
            'options' => ['An ORM', 'A template engine', 'A routing library'],
            'correct_option' => 0,
        ];

        $response = $this->actingAs($this->teacher)
            ->post(route('teacher.quizzes.questions.store', $this->quiz->id), $data);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question' => 'What is Eloquent?',
            'points' => 10,
        ]);

        $question = QuizQuestion::where('question', 'What is Eloquent?')->first();
        $this->assertNotNull($question);

        $this->assertDatabaseHas('quiz_options', [
            'question_id' => $question->id,
            'option_text' => 'An ORM',
            'is_correct' => true,
        ]);

        $this->assertDatabaseHas('quiz_options', [
            'question_id' => $question->id,
            'option_text' => 'A template engine',
            'is_correct' => false,
        ]);
    }

    public function test_non_teacher_cannot_access_quiz_questions_page()
    {
        $response = $this->actingAs($this->student)
            ->get(route('teacher.quizzes.questions', $this->quiz->id));

        $response->assertRedirect(route('home'));
    }

    public function test_teacher_can_access_student_profiles()
    {
        // Add student enrollment to teacher's course
        \App\Models\Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.students.index'));

        $response->assertStatus(200);

        $response = $this->actingAs($this->teacher)
            ->get(route('teacher.students.show', $this->student->id));

        $response->assertStatus(200)
            ->assertSee($this->student->name)
            ->assertSee($this->student->email);
    }
}
