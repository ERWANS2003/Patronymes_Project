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
            // Add indexes for optimized search performance
            $table->index('nom', 'idx_patronymes_nom');
            $table->index('signification', 'idx_patronymes_signification');
            $table->index('origine', 'idx_patronymes_origine');
            $table->index(['nom', 'signification'], 'idx_patronymes_nom_signification');
            $table->index('created_at', 'idx_patronymes_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patronymes', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_patronymes_nom');
            $table->dropIndex('idx_patronymes_signification');
            $table->dropIndex('idx_patronymes_origine');
            $table->dropIndex('idx_patronymes_nom_signification');
            $table->dropIndex('idx_patronymes_created_at');
        });
    }
};
