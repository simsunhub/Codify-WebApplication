<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $teacher;
    protected $category;
    protected $course;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a student and a teacher with English names
        $this->student = User::factory()->create(['role' => 'student', 'name' => 'Ivan Student']);
        $this->teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Petr Teacher']);

        // Create a category
        $this->category = Category::create([
            'name' => 'Programming',
            'slug' => 'programming',
            'is_active' => true,
        ]);

        // Create a course
        $this->course = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Basics of PHP',
            'slug' => 'basics-of-php',
            'description' => 'Learn the basics of PHP from scratch.',
            'price' => 0, // Free course
            'level' => 'beginner',
            'status' => 'published',
        ]);
    }

    public function test_user_can_enroll_in_free_course()
    {
        $response = $this->actingAs($this->student)->post(route('course.enroll', $this->course->slug));
        $response->assertRedirect();
        
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_user_can_unenroll_from_course()
    {
        // Enroll first
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($this->student)->delete(route('course.unenroll', $this->course->slug));
        $response->assertRedirect();

        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_user_can_access_checkout_page()
    {
        $paidCourse = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Advanced PHP',
            'slug' => 'advanced-php',
            'description' => 'Learn advanced PHP.',
            'price' => 99.99,
            'level' => 'advanced',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->student)->get(route('course.checkout', $paidCourse->slug));
        $response->assertStatus(200);
        $response->assertSee($paidCourse->title);
    }

    public function test_user_can_complete_checkout_paid_course()
    {
        $paidCourse = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Advanced PHP',
            'slug' => 'advanced-php',
            'description' => 'Learn advanced PHP.',
            'price' => 99.99,
            'level' => 'advanced',
            'status' => 'published',
        ]);

        $cardData = [
            'card_name' => 'Ivan Student',
            'card_number' => '1111222233334444',
            'card_expiry' => '12/29',
            'card_cvc' => '123',
        ];

        $response = $this->actingAs($this->student)->post(route('course.checkout.process', $paidCourse->slug), $cardData);
        $response->assertRedirect();

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $paidCourse->id,
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->student->id,
            'subtotal' => 99.99,
            'total' => 99.99,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('order_items', [
            'course_id' => $paidCourse->id,
            'price' => 99.99,
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->student->id,
            'amount' => 99.99,
            'status' => 'completed',
        ]);
    }

    public function test_course_completion_triggers_certificate_generation()
    {
        // Enroll first
        $this->actingAs($this->student)->post(route('course.enroll', $this->course->slug));

        // Create lessons
        $lesson1 = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 1',
            'content_text' => 'Content 1',
            'video_url' => 'https://youtube.com/watch?v=1',
            'sort_order' => 1,
        ]);

        $lesson2 = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 2',
            'content_text' => 'Content 2',
            'video_url' => 'https://youtube.com/watch?v=2',
            'sort_order' => 2,
        ]);

        // Mark first lesson complete
        $response1 = $this->actingAs($this->student)->post(route('lesson.complete', $lesson1->id));
        $response1->assertRedirect();
        
        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $lesson1->id,
        ]);

        // Certificate should NOT be created yet
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        // Mark second lesson complete
        $response2 = $this->actingAs($this->student)->post(route('lesson.complete', $lesson2->id));
        $response2->assertRedirect();

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $this->student->id,
            'lesson_id' => $lesson2->id,
        ]);

        // Certificate should be auto-created now!
        $this->assertDatabaseHas('certificates', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $certificate = Certificate::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->first();

        // Hit show certificate route to verify no TypeErrors or rendering issues
        $response = $this->get(route('certificates.show', $certificate->code));
        $response->assertStatus(200);
        $response->assertSee($this->student->name);
        $response->assertSee($this->course->title);

        // Hit download certificate route to verify PDF compilation and download
        $response = $this->get(route('certificates.download', $certificate->code));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_can_view_orders_index_and_details()
    {
        $paidCourse = Course::create([
            'user_id' => $this->teacher->id,
            'category_id' => $this->category->id,
            'title' => 'Advanced PHP',
            'slug' => 'advanced-php',
            'description' => 'Learn advanced PHP.',
            'price' => 99.99,
            'level' => 'advanced',
            'status' => 'published',
        ]);

        $cardData = [
            'card_name' => 'Ivan Student',
            'card_number' => '1111222233334444',
            'card_expiry' => '12/29',
            'card_cvc' => '123',
        ];

        // Perform checkout
        $this->actingAs($this->student)->post(route('course.checkout.process', $paidCourse->slug), $cardData);

        // Get the order
        $order = \App\Models\Order::where('user_id', $this->student->id)->first();

        // 1. Assert can view orders list page
        $responseIndex = $this->actingAs($this->student)->get(route('student.orders.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee($order->order_number);

        // 2. Assert can view order details page
        $responseShow = $this->actingAs($this->student)->get(route('student.orders.show', $order->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee($order->order_number);
        $responseShow->assertSee('Advanced PHP');
    }
}
