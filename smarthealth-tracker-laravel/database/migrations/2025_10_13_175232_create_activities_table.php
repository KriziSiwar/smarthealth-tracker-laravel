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
       Schema::create('activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('activity_type_id')->constrained('activity_types')->restrictOnDelete();
    $table->integer('duration'); // minutes
    $table->integer('calories_burned')->nullable();
    $table->enum('intensity', ['low','medium','high'])->default('medium');
    $table->text('notes')->nullable();
    $table->date('activity_date');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
