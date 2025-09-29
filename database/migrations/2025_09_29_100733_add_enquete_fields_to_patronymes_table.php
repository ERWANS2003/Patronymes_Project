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
            // Informations sur l'enquêté
            $table->string('enquete_nom')->nullable()->after('nom');
            $table->integer('enquete_age')->nullable()->after('enquete_nom');
            $table->enum('enquete_sexe', ['M', 'F'])->nullable()->after('enquete_age');
            $table->string('enquete_fonction')->nullable()->after('enquete_sexe');
            $table->string('enquete_contact')->nullable()->after('enquete_fonction');

            // Informations sur le patronyme
            $table->enum('transmission', ['pere', 'mere'])->nullable()->after('langue_id');
            $table->text('patronyme_sexe')->nullable()->after('transmission');
            $table->string('totem')->nullable()->after('patronyme_sexe');
            $table->text('justification_totem')->nullable()->after('totem');
            $table->text('parents_plaisanterie')->nullable()->after('justification_totem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropColumn([
                'enquete_nom',
                'enquete_age',
                'enquete_sexe',
                'enquete_fonction',
                'enquete_contact',
                'transmission',
                'patronyme_sexe',
                'totem',
                'justification_totem',
                'parents_plaisanterie'
            ]);
        });
    }
};
