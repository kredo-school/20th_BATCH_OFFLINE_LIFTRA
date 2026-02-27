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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('priority_type')->comment('1=important&urgent, 2=important&not urgent, 3=not important&urgent, 4=not important&not important');
            $table->unsignedTinyInteger('repeat_type')->comment('1=daily,2=weekly,3=monthly')->nullable();
            $table->unsignedInteger('repeat_interval')->default(1);
            $table->json('days_of_week')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
