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
        Schema::table('milestones', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->integer('order')->default(0)->after('completed_at');
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->integer('progress')->default(0)->after('target_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn(['description', 'completed_at', 'order']);
        });

        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
    }
};
