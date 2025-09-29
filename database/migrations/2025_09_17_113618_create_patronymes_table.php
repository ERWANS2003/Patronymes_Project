<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patronymes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('origine')->nullable();
            $table->text('signification')->nullable();
            $table->text('histoire')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('departement_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('frequence')->default(0);
            $table->timestamps();

            $table->index('nom');
            $table->index('region_id');
            $table->index('departement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patronymes');
    }
};
