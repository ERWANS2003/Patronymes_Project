<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add case-insensitive search indexes for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            // Create simple case-insensitive indexes for LIKE queries
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_nom_lower ON patronymes (lower(nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_signification_lower ON patronymes (lower(signification));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_origine_lower ON patronymes (lower(origine));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_histoire_lower ON patronymes (lower(histoire));');

            // Create indexes for regions, provinces, communes
            DB::statement('CREATE INDEX IF NOT EXISTS idx_regions_nom_lower ON regions (lower(nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_provinces_nom_lower ON provinces (lower(nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_communes_nom_lower ON communes (lower(nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_groupe_ethniques_nom_lower ON groupe_ethniques (lower(nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_langues_nom_lower ON langues (lower(nom));');

            // Create full-text search indexes for better performance
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_nom_fts ON patronymes USING gin(to_tsvector(\'french\', nom));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_signification_fts ON patronymes USING gin(to_tsvector(\'french\', signification));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_origine_fts ON patronymes USING gin(to_tsvector(\'french\', origine));');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_patronymes_histoire_fts ON patronymes USING gin(to_tsvector(\'french\', histoire));');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Drop the indexes
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_signification_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_origine_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_histoire_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_regions_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_provinces_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_communes_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_groupe_ethniques_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_langues_nom_lower;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_nom_fts;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_signification_fts;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_origine_fts;');
            DB::statement('DROP INDEX IF EXISTS idx_patronymes_histoire_fts;');
        }
    }
};
