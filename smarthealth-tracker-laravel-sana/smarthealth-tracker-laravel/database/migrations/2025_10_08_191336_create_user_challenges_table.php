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
        Schema::create('user_challenges', function (Blueprint $table) {
            $table->id();

            // 🔗 Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');

            // 📊 Suivi du challenge
            $table->integer('progress')->default(0); // ex: 50 (%)
            $table->boolean('completed')->default(false);
            $table->integer('score')->default(0); // points gagnés

            // 🕒 Période de participation
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // 📌 Statut du challenge pour cet utilisateur
            $table->enum('status', ['active', 'paused', 'completed', 'abandoned'])->default('active');

            // 🕓 Timestamps automatiques
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_challenges');
    }
};
