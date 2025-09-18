<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->text('contenu');
            $table->foreignId('patronyme_id')->constrained()->onDelete('cascade');
            $table->foreignId('contributeur_id')->constrained()->onDelete('cascade');
            $table->timestamp('date_contribution')->useCurrent();
            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
