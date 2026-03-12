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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedInteger('day_of_month')->nullable()->after('days_of_week');
            $table->time('task_time')->nullable()->after('day_of_month');
            $table->date('due_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['day_of_month', 'task_time']);
            $table->date('due_date')->nullable(false)->change();
        });
    }
};
