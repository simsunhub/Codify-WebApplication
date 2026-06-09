<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->text('instructions')->nullable();
            $table->datetime('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->integer('max_file_size')->default(10);
            $table->string('allowed_extensions')->nullable()->default('pdf,doc,docx,zip');
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('assignments');
    }
};