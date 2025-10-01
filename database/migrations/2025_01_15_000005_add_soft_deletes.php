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
        // Ajouter soft deletes aux tables principales (seulement si les colonnes n'existent pas déjà)
        // Les colonnes deleted_at ont déjà été ajoutées aux tables patronymes et users

        Schema::table('commentaires', function (Blueprint $table) {
            if (!Schema::hasColumn('commentaires', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('contributions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commentaires', function (Blueprint $table) {
            if (Schema::hasColumn('commentaires', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('contributions', function (Blueprint $table) {
            if (Schema::hasColumn('contributions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
