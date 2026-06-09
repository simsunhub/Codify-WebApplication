<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
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

    public function test_admin_can_access_analytics_page(): void
    {
        // Create a completed order with items to test the queries
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test Description',
            'price' => 50.00,
            'status' => 'published',
            'level' => 'beginner',
        ]);

        $order = Order::create([
            'user_id' => $this->admin->id,
            'subtotal' => 50.00,
            'discount' => 0.00,
            'total' => 50.00,
            'status' => 'completed',
            'payment_method' => 'card',
            'completed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 50.00,
            'instructor_earning' => 40.00,
            'platform_fee' => 10.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertViewHas([
            'revenueMonths',
            'revenueTrend',
            'platformShareTrend',
            'userMonths',
            'studentTrend',
            'teacherTrend',
            'courseChart',
            'langChart',
            'codingStatusChart',
        ]);
    }

    public function test_admin_can_access_revenue_page(): void
    {
        // Create a completed order with items to test the queries
        $course = Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => 'Test Course 2',
            'slug' => 'test-course-2',
            'description' => 'Test Description',
            'price' => 100.00,
            'status' => 'published',
            'level' => 'beginner',
        ]);

        $order = Order::create([
            'user_id' => $this->admin->id,
            'subtotal' => 100.00,
            'discount' => 0.00,
            'total' => 100.00,
            'status' => 'completed',
            'payment_method' => 'card',
            'completed_at' => now(),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'price' => 100.00,
            'instructor_earning' => 80.00,
            'platform_fee' => 20.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.revenue.index'));

        $response->assertStatus(200);
        $response->assertViewHas([
            'totalSales',
            'systemEarning',
            'instructorEarning',
            'withdrawals',
        ]);
    }

    public function test_admin_can_access_coding_problems_page(): void
    {
        $lang = \App\Models\ProgrammingLanguage::create([
            'name' => 'Python',
            'slug' => 'python',
            'is_active' => true,
        ]);

        $problem = \App\Models\CodingProblem::create([
            'title' => 'Two Sum',
            'slug' => 'two-sum',
            'description' => 'Solve two sum problem',
            'difficulty' => 'easy',
            'category' => 'Arrays',
            'created_by' => $this->instructor->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.coding.index'));

        $response->assertStatus(200);
        $response->assertViewHas([
            'languages',
            'problems',
        ]);
        $response->assertSee('Two Sum');
        $response->assertSee('Python');
    }
}
