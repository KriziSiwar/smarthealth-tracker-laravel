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
        if (!Schema::hasTable('meal_plan_food')) {
            Schema::create('meal_plan_food', function (Blueprint $table) {
                $table->id();
                
                // Make sure table names are correct
                $table->foreignId('meal_plan_id')->constrained('meal_plans')->cascadeOnDelete();
                $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
                
                $table->decimal('serving_size', 10, 2)->nullable();
                $table->string('serving_unit', 50)->nullable();
                $table->string('meal_type')->nullable(); // ex: breakfast, lunch, dinner
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plan_food');
    }
};
