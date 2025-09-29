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
        Schema::create('ethnie_groupe_ethnique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_ethnique_id')->constrained()->onDelete('cascade');
            $table->foreignId('ethnie_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['groupe_ethnique_id', 'ethnie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnie_groupe_ethnique');
    }
};
