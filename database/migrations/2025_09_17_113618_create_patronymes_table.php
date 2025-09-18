<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            // Supprimer les anciennes colonnes si nécessaire
            $table->dropForeign(['region_id']);
            $table->dropForeign(['departement_id']);
            $table->dropColumn(['region_id', 'departement_id', 'origine', 'signification', 'histoire', 'frequence']);

            // Ajouter les nouvelles colonnes
            $table->text('signification')->nullable();
            $table->text('origine')->nullable();
            $table->foreignId('province_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('commune_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('groupe_ethnique_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('langue_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('mode_transmission_id')->nullable()->constrained()->onDelete('set null');

            // Réindexer
            $table->index('groupe_ethnique_id');
            $table->index('langue_id');
        });
    }

    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['commune_id']);
            $table->dropForeign(['groupe_ethnique_id']);
            $table->dropForeign(['langue_id']);
            $table->dropForeign(['mode_transmission_id']);

            $table->dropColumn([
                'province_id',
                'commune_id',
                'groupe_ethnique_id',
                'langue_id',
                'mode_transmission_id',
                'signification',
                'origine'
            ]);

            // Recréer les anciennes colonnes
            $table->text('origine')->nullable();
            $table->text('signification')->nullable();
            $table->text('histoire')->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('departement_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('frequence')->default(0);
        });
    }
};
