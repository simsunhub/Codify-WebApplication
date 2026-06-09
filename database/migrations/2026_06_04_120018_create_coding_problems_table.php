<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coding_problems', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('difficulty')->default('easy');
            $table->string('category')->nullable();
            $table->text('constraints')->nullable();
            $table->json('hints')->nullable();
            $table->text('solution_code')->nullable();
            $table->json('starter_code')->nullable();
            $table->integer('time_limit_ms')->default(2000);
            $table->integer('memory_limit_kb')->default(262144);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('solved_count')->default(0);
            $table->integer('attempt_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('coding_problems');
    }
};