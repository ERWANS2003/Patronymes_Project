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
        // Indexes pour la table patronymes
        Schema::table('patronymes', function (Blueprint $table) {
            // Index composite pour les recherches fréquentes
            $table->index(['region_id', 'groupe_ethnique_id'], 'idx_region_groupe');
            $table->index(['langue_id', 'patronyme_sexe'], 'idx_langue_sexe');
            $table->index(['views_count', 'created_at'], 'idx_popular_recent');
            $table->index(['is_featured', 'views_count'], 'idx_featured_popular');
            
            // Index pour les recherches textuelles
            $table->index(['nom', 'signification'], 'idx_nom_signification');
            $table->index(['origine', 'histoire'], 'idx_origine_histoire');
            
            // Index pour les filtres
            $table->index('transmission', 'idx_transmission');
            $table->index('patronyme_sexe', 'idx_patronyme_sexe');
            $table->index('frequence', 'idx_frequence');
        });
        
        // Indexes pour la table users
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_active'], 'idx_role_active');
            $table->index(['can_contribute', 'can_manage_roles'], 'idx_permissions');
            $table->index('last_login_at', 'idx_last_login');
            $table->index('login_count', 'idx_login_count');
        });
        
        // Indexes pour la table commentaires
        Schema::table('commentaires', function (Blueprint $table) {
            $table->index(['patronyme_id', 'created_at'], 'idx_patronyme_created');
            $table->index(['utilisateur_id', 'created_at'], 'idx_user_created');
        });
        
        // Indexes pour la table favorites
        Schema::table('favorites', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_user_favorites');
            $table->index(['patronyme_id', 'created_at'], 'idx_patronyme_favorites');
        });
        
        // Indexes pour les tables de référence
        Schema::table('regions', function (Blueprint $table) {
            $table->index('name', 'idx_region_name');
        });
        
        Schema::table('provinces', function (Blueprint $table) {
            $table->index(['region_id', 'nom'], 'idx_province_region_name');
        });
        
        Schema::table('communes', function (Blueprint $table) {
            $table->index(['province_id', 'nom'], 'idx_commune_province_name');
        });
        
        Schema::table('groupe_ethniques', function (Blueprint $table) {
            $table->index('nom', 'idx_groupe_nom');
        });
        
        Schema::table('langues', function (Blueprint $table) {
            $table->index('nom', 'idx_langue_nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropIndex('idx_region_groupe');
            $table->dropIndex('idx_langue_sexe');
            $table->dropIndex('idx_popular_recent');
            $table->dropIndex('idx_featured_popular');
            $table->dropIndex('idx_nom_signification');
            $table->dropIndex('idx_origine_histoire');
            $table->dropIndex('idx_transmission');
            $table->dropIndex('idx_patronyme_sexe');
            $table->dropIndex('idx_frequence');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_role_active');
            $table->dropIndex('idx_permissions');
            $table->dropIndex('idx_last_login');
            $table->dropIndex('idx_login_count');
        });
        
        Schema::table('commentaires', function (Blueprint $table) {
            $table->dropIndex('idx_patronyme_created');
            $table->dropIndex('idx_user_created');
        });
        
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropIndex('idx_user_favorites');
            $table->dropIndex('idx_patronyme_favorites');
        });
        
        Schema::table('regions', function (Blueprint $table) {
            $table->dropIndex('idx_region_name');
        });
        
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropIndex('idx_province_region_name');
        });
        
        Schema::table('communes', function (Blueprint $table) {
            $table->dropIndex('idx_commune_province_name');
        });
        
        Schema::table('groupe_ethniques', function (Blueprint $table) {
            $table->dropIndex('idx_groupe_nom');
        });
        
        Schema::table('langues', function (Blueprint $table) {
            $table->dropIndex('idx_langue_nom');
        });
    }
};
