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
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();

            // 🏷️ Informations de base
            $table->string('title');
            $table->text('description')->nullable();

            // 📅 Période
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // 🎯 Objectif
            $table->integer('target_value')->nullable(); // ex : boire 2000 ml par jour
            $table->string('unit')->nullable(); // ex : ml, kg, km, steps

            // 💪 Paramètres de motivation
            $table->integer('reward_points')->default(0);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');

            // 🔖 Catégorisation
            $table->string('category')->nullable(); // ex : "Hydratation", "Fitness", "Sommeil"

            // 📊 Statut du challenge
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');

            // 🧑‍💼 Créateur du challenge
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
