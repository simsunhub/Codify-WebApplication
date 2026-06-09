<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add is_premium to users
        if (!Schema::hasColumn('users', 'is_premium')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_premium')->default(false)->after('bio');
            });
        }

        // 2. Create lms_modules
        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->unique();
            $table->boolean('is_enabled')->default(true);
            $table->enum('accessible_by', ['all', 'premium_only', 'disabled'])->default('all');
            $table->integer('max_limit')->nullable();
            $table->timestamps();
        });

        // 3. Create course_module_settings
        Schema::create('course_module_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('module_name');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'module_name']);
        });

        // Pre-fill the 11 modules
        $modules = [
            ['module_name' => 'my_learning', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'practice', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'assignments', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'quizzes', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'wishlist', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'messages', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'certificates', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'purchases', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'playlist', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'watch_later', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'profile', 'is_enabled' => true, 'accessible_by' => 'all', 'max_limit' => null, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('lms_modules')->insert($modules);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_module_settings');
        Schema::dropIfExists('lms_modules');

        if (Schema::hasColumn('users', 'is_premium')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_premium');
            });
        }
    }
};
