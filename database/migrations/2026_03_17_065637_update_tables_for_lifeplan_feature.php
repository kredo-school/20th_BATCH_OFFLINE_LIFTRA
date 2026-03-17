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
        $colors = [
            ['id' => 1, 'name' => 'Blue', 'code' => '#6366F1'],
            ['id' => 2, 'name' => 'Green', 'code' => '#22C55E'],
            ['id' => 3, 'name' => 'Yellow', 'code' => '#FBBF24'],
            ['id' => 4, 'name' => 'Red', 'code' => '#EF4444'],
            ['id' => 5, 'name' => 'Purple', 'code' => '#A855F7'],
            ['id' => 6, 'name' => 'Teal', 'code' => '#38BDF8'],
            ['id' => 7, 'name' => 'Orange', 'code' => '#F97316'],
            ['id' => 8, 'name' => 'Dark Gray', 'code' => '#4B5563'],
        ];
        foreach($colors as $color) {
            \App\Models\Color::updateOrCreate(['id' => $color['id']], $color);
        }

        $icons = [
            ['id' => 1, 'name' => 'Folder', 'class' => 'fa-folder'],
            ['id' => 2, 'name' => 'Book', 'class' => 'fa-book'],
            ['id' => 3, 'name' => 'Briefcase', 'class' => 'fa-briefcase'],
            ['id' => 4, 'name' => 'Home', 'class' => 'fa-house'],
            ['id' => 5, 'name' => 'Dumbbell', 'class' => 'fa-dumbbell'],
            ['id' => 6, 'name' => 'Heart', 'class' => 'fa-heart'],
            ['id' => 7, 'name' => 'Target', 'class' => 'fa-bullseye'],
            ['id' => 8, 'name' => 'Pen', 'class' => 'fa-pen'],
            ['id' => 9, 'name' => 'Cup', 'class' => 'fa-mug-hot'],
            ['id' => 10, 'name' => 'Cart', 'class' => 'fa-cart-shopping'],
            ['id' => 11, 'name' => 'Airplane', 'class' => 'fa-plane'],
            ['id' => 12, 'name' => 'Music', 'class' => 'fa-music'],
        ];
        foreach($icons as $icon) {
            \App\Models\Icon::updateOrCreate(['id' => $icon['id']], $icon);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['color', 'icon']);
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('icon_id')->nullable()->constrained('icons')->nullOnDelete();
        });

        if (!Schema::hasTable('actions')) {
            Schema::create('actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('milestone_id')->constrained('milestones')->cascadeOnDelete();
                $table->string('title');
                $table->boolean('completed')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['color_id']);
            $table->dropForeign(['icon_id']);
            $table->dropColumn(['color_id', 'icon_id']);
            $table->unsignedTinyInteger('icon')->default(1);
            $table->unsignedTinyInteger('color')->default(1);
        });
    }
};
