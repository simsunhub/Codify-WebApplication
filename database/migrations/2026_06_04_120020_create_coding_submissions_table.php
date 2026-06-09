<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coding_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('problem_id')->constrained('coding_problems')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('programming_languages')->cascadeOnDelete();
            $table->text('code');
            $table->string('status')->default('pending');
            $table->integer('runtime_ms')->nullable();
            $table->integer('memory_kb')->nullable();
            $table->json('test_results')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->index(['user_id', 'problem_id']);
            $table->index('status');
        });
    }
    public function down(): void {
        Schema::dropIfExists('coding_submissions');
    }
};