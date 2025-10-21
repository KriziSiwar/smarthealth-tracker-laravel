<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('nutrition_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Macronutrients
            $table->integer('calories')->default(2000);
            $table->integer('protein')->default(150);
            $table->integer('carbohydrates')->default(250);
            $table->integer('fat')->default(67);
            
            // Micronutrients
            $table->integer('fiber')->default(30);
            $table->integer('sugar')->default(50);
            $table->integer('sodium')->default(2300);
            $table->integer('cholesterol')->default(300);
            
            // Water intake goal in milliliters
            $table->integer('water_goal_ml')->default(2500);
            
            // User preferences
            $table->enum('activity_level', ['sedentary', 'lightly_active', 'moderately_active', 'very_active', 'extra_active'])->default('moderately_active');
            $table->enum('goal_type', ['maintain', 'lose_weight', 'gain_weight', 'build_muscle'])->default('maintain');
            
            // Tracking preferences
            $table->boolean('track_water')->default(true);
            $table->boolean('track_macros')->default(true);
            $table->boolean('track_micros')->default(false);
            $table->boolean('receive_reminders')->default(true);
            
            $table->timestamps();
            
            // Ensure one-to-one relationship with users
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('nutrition_goals');
    }
};
