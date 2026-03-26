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
        Schema::table('users', function (Blueprint $col) {
            $col->dropColumn('has_completed_tour');
            $col->boolean('tour_home_completed')->default(false)->after('profile_image');
            $col->boolean('tour_category_completed')->default(false)->after('tour_home_completed');
            $col->boolean('tour_milestone_completed')->default(false)->after('tour_category_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $col) {
            $col->boolean('has_completed_tour')->default(false)->after('profile_image');
            $col->dropColumn(['tour_home_completed', 'tour_category_completed', 'tour_milestone_completed']);
        });
    }
};
