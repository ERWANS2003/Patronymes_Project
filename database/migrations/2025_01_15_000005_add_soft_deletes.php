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
        // Ajouter soft deletes aux tables principales
        Schema::table('patronymes', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('commentaires', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('contributions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
