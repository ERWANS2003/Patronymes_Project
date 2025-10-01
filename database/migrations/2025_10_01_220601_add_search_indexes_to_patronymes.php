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
            // Index pour la recherche textuelle optimisée (vérifier l'existence)
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_nom')) {
                $table->index('nom', 'idx_patronymes_nom');
            }
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_signification')) {
                $table->index('signification', 'idx_patronymes_signification');
            }
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_origine')) {
                $table->index('origine', 'idx_patronymes_origine');
            }
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_histoire')) {
                $table->index('histoire', 'idx_patronymes_histoire');
            }

            // Index composite pour les recherches fréquentes
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_location')) {
                $table->index(['region_id', 'province_id', 'commune_id'], 'idx_patronymes_location');
            }
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_ethnicity')) {
                $table->index(['groupe_ethnique_id', 'ethnie_id'], 'idx_patronymes_ethnicity');
            }

            // Index pour les statistiques
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_popularity')) {
                $table->index(['views_count', 'created_at'], 'idx_patronymes_popularity');
            }
            if (!Schema::hasIndex('patronymes', 'idx_patronymes_frequency')) {
                $table->index(['frequence', 'views_count'], 'idx_patronymes_frequency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropIndex('idx_patronymes_nom');
            $table->dropIndex('idx_patronymes_signification');
            $table->dropIndex('idx_patronymes_origine');
            $table->dropIndex('idx_patronymes_histoire');
            $table->dropIndex('idx_patronymes_location');
            $table->dropIndex('idx_patronymes_ethnicity');
            $table->dropIndex('idx_patronymes_popularity');
            $table->dropIndex('idx_patronymes_frequency');
        });
    }
};
