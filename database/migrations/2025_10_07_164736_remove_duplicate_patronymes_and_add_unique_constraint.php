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
        // Étape 1: Identifier et supprimer les doublons
        $this->removeDuplicatePatronymes();

        // Étape 2: Ajouter une contrainte d'unicité sur le nom
        Schema::table('patronymes', function (Blueprint $table) {
            $table->unique('nom', 'patronymes_nom_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte d'unicité
        Schema::table('patronymes', function (Blueprint $table) {
            $table->dropUnique('patronymes_nom_unique');
        });
    }

    /**
     * Supprimer les patronymes dupliqués en gardant le plus récent ou celui avec le plus de vues
     */
    private function removeDuplicatePatronymes(): void
    {
        // Identifier les doublons
        $duplicates = DB::table('patronymes')
            ->select('nom', DB::raw('COUNT(*) as count'))
            ->groupBy('nom')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            // Pour chaque nom dupliqué, garder le meilleur (plus récent ou plus de vues)
            $patronymes = DB::table('patronymes')
                ->where('nom', $duplicate->nom)
                ->orderBy('views_count', 'desc') // Priorité aux plus populaires
                ->orderBy('created_at', 'desc')   // Puis aux plus récents
                ->get();

            // Garder le premier (meilleur) et supprimer les autres
            $toKeep = $patronymes->first();
            $toDelete = $patronymes->skip(1);

            foreach ($toDelete as $patronyme) {
                echo "Suppression du doublon: {$patronyme->nom} (ID: {$patronyme->id})\n";
                DB::table('patronymes')->where('id', $patronyme->id)->delete();
            }
        }
    }
};
