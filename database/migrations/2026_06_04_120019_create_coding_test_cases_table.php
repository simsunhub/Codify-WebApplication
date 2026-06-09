<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coding_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained('coding_problems')->cascadeOnDelete();
            $table->text('input');
            $table->text('expected_output');
            $table->boolean('is_sample')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('coding_test_cases');
    }
};