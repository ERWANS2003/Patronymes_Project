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
        // Optimisations pour la table patronymes (seulement sur les colonnes existantes)
        Schema::table('patronymes', function (Blueprint $table) {
            // Index pour les recherches textuelles
            $table->index(['nom'], 'idx_patronymes_nom');
            $table->index(['signification'], 'idx_patronymes_signification');
            // $table->index(['origine'], 'idx_patronymes_origine');

            // Index composites pour les filtres fréquents
            // $table->index(['region_id', 'views_count'], 'idx_patronymes_region_views');
            // $table->index(['groupe_ethnique_id', 'views_count'], 'idx_patronymes_groupe_views');
            // $table->index(['langue_id', 'created_at'], 'idx_patronymes_langue_created');

            // Index pour les statistiques
            // $table->index(['views_count', 'created_at'], 'idx_patronymes_views_created');
            // $table->index(['is_featured', 'views_count'], 'idx_patronymes_featured_views');
            // $table->index(['created_at', 'views_count'], 'idx_patronymes_created_views');

            // Index pour les recherches géographiques
            // $table->index(['region_id', 'province_id', 'commune_id'], 'idx_patronymes_location');

            // Index pour les filtres de sexe et transmission
            // $table->index(['patronyme_sexe', 'transmission'], 'idx_patronymes_sexe_transmission');

            // Index pour la fréquence
            // $table->index(['frequence'], 'idx_patronymes_frequence');
        });

        // Optimisations pour la table users (seulement sur les colonnes existantes)
        Schema::table('users', function (Blueprint $table) {
            // $table->index(['role', 'is_active'], 'idx_users_role_active');
            // $table->index(['can_contribute', 'created_at'], 'idx_users_contribute_created');
            // $table->index(['last_login_at'], 'idx_users_last_login');
            // $table->index(['login_count'], 'idx_users_login_count');
        });

        // Optimisations pour la table favorites
        Schema::table('favorites', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_favorites_user_created');
            $table->index(['patronyme_id', 'created_at'], 'idx_favorites_patronyme_created');
        });

        // Optimisations pour la table commentaires
        Schema::table('commentaires', function (Blueprint $table) {
            $table->index(['patronyme_id', 'created_at'], 'idx_commentaires_patronyme_created');
            $table->index(['utilisateur_id', 'created_at'], 'idx_commentaires_user_created');
        });

        // Optimisations pour les tables de référence (seulement sur les colonnes existantes)
        Schema::table('regions', function (Blueprint $table) {
            $table->index(['name'], 'idx_regions_name');
            // $table->index(['code'], 'idx_regions_code');
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->index(['region_id', 'nom'], 'idx_provinces_region_nom');
        });

        Schema::table('communes', function (Blueprint $table) {
            $table->index(['province_id', 'nom'], 'idx_communes_province_nom');
        });

        Schema::table('groupe_ethniques', function (Blueprint $table) {
            $table->index(['nom'], 'idx_groupe_ethniques_nom');
        });

        // Schema::table('ethnies', function (Blueprint $table) {
        //     $table->index(['nom'], 'idx_ethnies_nom');
        // });

        Schema::table('langues', function (Blueprint $table) {
            $table->index(['nom'], 'idx_langues_nom');
        });

        // Schema::table('mode_transmissions', function (Blueprint $table) {
        //     $table->index(['nom'], 'idx_mode_transmissions_nom');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les index ajoutés
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropIndex('idx_patronymes_nom');
            $table->dropIndex('idx_patronymes_signification');
            $table->dropIndex('idx_patronymes_origine');
            $table->dropIndex('idx_patronymes_region_views');
            $table->dropIndex('idx_patronymes_groupe_views');
            $table->dropIndex('idx_patronymes_langue_created');
            $table->dropIndex('idx_patronymes_views_created');
            $table->dropIndex('idx_patronymes_featured_views');
            $table->dropIndex('idx_patronymes_created_views');
            $table->dropIndex('idx_patronymes_location');
            $table->dropIndex('idx_patronymes_sexe_transmission');
            $table->dropIndex('idx_patronymes_frequence');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_active');
            $table->dropIndex('idx_users_contribute_created');
            $table->dropIndex('idx_users_last_login');
            $table->dropIndex('idx_users_login_count');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex('idx_favorites_user_created');
            $table->dropIndex('idx_favorites_patronyme_created');
        });

        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropIndex('idx_commentaires_patronyme_created');
            $table->dropIndex('idx_commentaires_user_created');
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex('idx_regions_name');
            $table->dropIndex('idx_regions_code');
        });

        Schema::table('provinces', function (Blueprint $table) {
            $table->dropIndex('idx_provinces_region_nom');
        });

        Schema::table('communes', function (Blueprint $table) {
            $table->dropIndex('idx_communes_province_nom');
        });

        Schema::table('groupe_ethniques', function (Blueprint $table) {
            $table->dropIndex('idx_groupe_ethniques_nom');
        });

        Schema::table('ethnies', function (Blueprint $table) {
            $table->dropIndex('idx_ethnies_nom');
        });

        Schema::table('langues', function (Blueprint $table) {
            $table->dropIndex('idx_langues_nom');
        });

        Schema::table('mode_transmissions', function (Blueprint $table) {
            $table->dropIndex('idx_mode_transmissions_nom');
        });
    }
};
