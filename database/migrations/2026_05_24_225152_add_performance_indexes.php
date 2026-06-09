<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['user_id', 'course_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['course_id', 'sort_order']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['course_id', 'rating']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->index(['user_id', 'lesson_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'course_id']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'sort_order']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'rating']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'lesson_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id', 'receiver_id']);
        });
    }
};
