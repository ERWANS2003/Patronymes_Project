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
        Schema::table('patronymes', function (Blueprint $table) {
            // Section I: Champs de l'enquêté
            if (!Schema::hasColumn('patronymes', 'date_collecte')) {
                $table->date('date_collecte')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'collecteur')) {
                $table->string('collecteur')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'code_fiche')) {
                $table->string('code_fiche')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'contact')) {
                $table->string('contact')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_nom')) {
                $table->string('enquete_nom')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_age')) {
                $table->integer('enquete_age')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_sexe')) {
                $table->enum('enquete_sexe', ['M', 'F'])->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_fonction')) {
                $table->string('enquete_fonction')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_telephone')) {
                $table->string('enquete_telephone')->nullable();
            }
            if (!Schema::hasColumn('patronymes', 'enquete_email')) {
                $table->string('enquete_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropColumn([
                'date_collecte',
                'collecteur',
                'code_fiche',
                'contact',
                'enquete_nom',
                'enquete_age',
                'enquete_sexe',
                'enquete_fonction',
                'enquete_telephone',
                'enquete_email'
            ]);
        });
    }
};
