<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        return [
            'instructor_id' => User::where('role', 'instructor')->first()?->id ?? User::factory()->state(['role' => 'instructor']),
            'category_id' => Category::first()?->id ?? Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(4),
            'price' => fake()->randomFloat(2, 19, 99),
            'image_path' => null,
            'status' => 'published',
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
        ];
    }
}
