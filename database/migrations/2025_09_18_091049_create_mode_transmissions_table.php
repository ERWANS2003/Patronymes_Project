<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents_a_plaisanterie', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethnie_1_id')->constrained('ethnies')->onDelete('cascade');
            $table->foreignId('ethnie_2_id')->constrained('ethnies')->onDelete('cascade');
            $table->text('description');
            $table->timestamps();

            $table->unique(['ethnie_1_id', 'ethnie_2_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents_a_plaisanterie');
    }
};
