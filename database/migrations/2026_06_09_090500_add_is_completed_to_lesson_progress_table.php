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
        if (Schema::hasTable('lesson_progress') && !Schema::hasColumn('lesson_progress', 'is_completed')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->boolean('is_completed')->default(false)->after('lesson_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lesson_progress') && Schema::hasColumn('lesson_progress', 'is_completed')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->dropColumn('is_completed');
            });
        }
    }
};
