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
    Schema::table('user_challenges', function (Blueprint $table) {
        // Soit changer en string
        $table->string('status', 20)->default('active')->change();
        
        // Soit définir les valeurs ENUM autorisées
        // $table->enum('status', ['active', 'completed', 'cancelled'])->default('active')->change();
    });
}

public function down()
{
    Schema::table('user_challenges', function (Blueprint $table) {
        $table->string('status', 20)->default('active')->change();
    });
}
};
