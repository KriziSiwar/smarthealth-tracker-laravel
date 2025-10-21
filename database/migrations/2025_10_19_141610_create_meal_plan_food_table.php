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
        Schema::create('meal_plan_food', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_id')->constrained()->onDelete('cascade');
            $table->decimal('serving_size', 10, 2);
            $table->string('serving_unit', 50);
            $table->string('meal_type'); // e.g., 'breakfast', 'lunch', 'dinner', 'snack'
            $table->timestamps();
            
            // Ensure each food can only be added once per meal plan and meal type
            $table->unique(['meal_plan_id', 'food_id', 'meal_type'], 'meal_plan_food_meal_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_food');
    }
};
