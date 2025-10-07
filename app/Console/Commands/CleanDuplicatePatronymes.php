<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Patronyme;

class CleanDuplicatePatronymes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patronymes:clean-duplicates {--dry-run : Afficher les doublons sans les supprimer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les patronymes dupliqués en gardant le meilleur de chaque groupe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Recherche des patronymes dupliqués...');

        // Identifier les doublons
        $duplicates = DB::table('patronymes')
            ->select('nom', DB::raw('COUNT(*) as count'))
            ->groupBy('nom')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ Aucun doublon trouvé !');
            return 0;
        }

        $this->warn("⚠️  {$duplicates->count()} patronymes dupliqués trouvés :");

        $totalToDelete = 0;

        foreach ($duplicates as $duplicate) {
            $this->line("📝 {$duplicate->nom} ({$duplicate->count} occurrences)");

            // Récupérer tous les patronymes avec ce nom
            $patronymes = Patronyme::where('nom', $duplicate->nom)
                ->orderBy('views_count', 'desc') // Priorité aux plus populaires
                ->orderBy('created_at', 'desc')   // Puis aux plus récents
                ->get();

            $toKeep = $patronymes->first();
            $toDelete = $patronymes->skip(1);

            $this->line("   ✅ À conserver : ID {$toKeep->id} (vues: {$toKeep->views_count}, créé: {$toKeep->created_at->format('Y-m-d H:i')})");

            foreach ($toDelete as $patronyme) {
                $this->line("   ❌ À supprimer : ID {$patronyme->id} (vues: {$patronyme->views_count}, créé: {$patronyme->created_at->format('Y-m-d H:i')})");
                $totalToDelete++;
            }

            if (!$isDryRun) {
                // Supprimer les doublons
                foreach ($toDelete as $patronyme) {
                    $patronyme->delete();
                    $this->info("   🗑️  Supprimé : ID {$patronyme->id}");
                }
            }

            $this->line('');
        }

        if ($isDryRun) {
            $this->warn("🔍 MODE DRY-RUN : {$totalToDelete} patronymes seraient supprimés");
            $this->info('💡 Utilisez --dry-run=false pour effectuer la suppression');
        } else {
            $this->info("✅ Nettoyage terminé : {$totalToDelete} patronymes supprimés");

            // Vérifier le résultat
            $finalCount = Patronyme::count();
            $uniqueCount = Patronyme::distinct('nom')->count('nom');

            $this->info("📊 Résultat final :");
            $this->info("   - Total patronymes : {$finalCount}");
            $this->info("   - Patronymes uniques : {$uniqueCount}");

            if ($finalCount === $uniqueCount) {
                $this->info("✅ Aucun doublon restant !");
            } else {
                $this->warn("⚠️  Il reste encore des doublons !");
            }
        }

        return 0;
    }
}
