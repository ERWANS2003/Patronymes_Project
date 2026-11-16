<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patronyme;
use App\Models\Region;
use App\Models\GroupeEthnique;
use App\Models\Langue;

class TestPatronymesSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les données de base
        $regions = Region::all();
        $groupesEthniques = GroupeEthnique::all();
        $langues = Langue::all();

        // 60 patronymes burkinabés réalistes
        $patronymes = [
            ['nom' => 'Ouédraogo', 'signification' => 'Un grand roi en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Sawadogo', 'signification' => 'Celui qui tisse en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Traoré', 'signification' => 'Le puissant ou le guerrier en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Kaboré', 'signification' => 'Gouverneur ou chef en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Zongo', 'signification' => 'Quartier des marchands', 'groupe_ethnique' => 'Haoussa', 'langue' => 'Haoussa'],
            ['nom' => 'Ba', 'signification' => 'Père en peul et mandingue', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Diallo', 'signification' => 'Guerrier en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Sy', 'signification' => 'Chef de famille en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Sow', 'signification' => 'Prince en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Diop', 'signification' => 'Lion en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Cissé', 'signification' => 'Celui qui prie en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Coulibaly', 'signification' => 'Celui qui maîtrise la foudre', 'groupe_ethnique' => 'Sénoufo', 'langue' => 'Sénoufo'],
            ['nom' => 'Toure', 'signification' => 'Fondateur, créateur en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Koïta', 'signification' => 'Noble en bambara', 'groupe_ethnique' => 'Bobo', 'langue' => 'Bobo'],
            ['nom' => 'Doula', 'signification' => 'Chef guerrier', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Kone', 'signification' => 'Homme de bien en dioula', 'groupe_ethnique' => 'Sénoufo', 'langue' => 'Sénoufo'],
            ['nom' => 'Soumaré', 'signification' => 'Celui qui transmet en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Zerbo', 'signification' => 'Guerrier courageux en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Somé', 'signification' => 'Celui qui donne en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Bonkoungou', 'signification' => 'Grand chasseur en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Hilary', 'signification' => 'Joyeux, gai en latin', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Hema', 'signification' => 'Le piroguier en dagara', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Guiro', 'signification' => 'Celui qui combat en mandingue', 'groupe_ethnique' => 'Bobo', 'langue' => 'Bobo'],
            ['nom' => 'Waiguimdé', 'signification' => 'Celui qui fait la paix en lobi', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Tapoko', 'signification' => 'Chef de village en lobi', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Samba', 'signification' => 'Guerrier en mandingue', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Ndiaye', 'signification' => 'Celui qui se lève tôt', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Kamara', 'signification' => 'Chef de clan en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Bakayoko', 'signification' => 'Celui qui chante la justice', 'groupe_ethnique' => 'Sénoufo', 'langue' => 'Sénoufo'],
            ['nom' => 'Dembélé', 'signification' => 'Celui qui parle bien en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            // 30 patronymes supplémentaires
            ['nom' => 'Tankoano', 'signification' => 'Celui qui herrite la terre', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Béré', 'signification' => 'Protecteur en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Lankoandé', 'signification' => 'Celui qui donne le bien', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Paré', 'signification' => 'Guerrier riche en mooré', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Tiendrebeogo', 'signification' => 'Celui qui cherche le bien', 'groupe_ethnique' => 'Mossi', 'langue' => 'Mooré'],
            ['nom' => 'Saldé', 'signification' => 'Chef de famille en lobi', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Yanza', 'signification' => 'Celui qui comprend', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Sampiri', 'signification' => 'Maître de l\'eau en lobi', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Bayala', 'signification' => 'Celui de la forge', 'groupe_ethnique' => 'Lobi', 'langue' => 'Lobi'],
            ['nom' => 'Yaro', 'signification' => 'Guerrier de race en haoussa', 'groupe_ethnique' => 'Haoussa', 'langue' => 'Haoussa'],
            ['nom' => 'Garba', 'signification' => 'Forgeron en haoussa', 'groupe_ethnique' => 'Haoussa', 'langue' => 'Haoussa'],
            ['nom' => 'Mallam', 'signification' => 'Maître religieux en haoussa', 'groupe_ethnique' => 'Haoussa', 'langue' => 'Haoussa'],
            ['nom' => 'Isya', 'signification' => 'Celui qui a reçu la grâce', 'groupe_ethnique' => 'Haoussa', 'langue' => 'Haoussa'],
            ['nom' => 'Dembele', 'signification' => 'Celui qui se mouvement en langue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Koroma', 'signification' => 'Chef de clan important', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Bah', 'signification' => 'Celui qui suit en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Diaw', 'signification' => 'Puissance en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Kane', 'signification' => 'Prince en peul', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Njie', 'signification' => 'Celui qui vit juste', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Biaye', 'signification' => 'Celui qui porte la lumière', 'groupe_ethnique' => 'Peul', 'langue' => 'Peul'],
            ['nom' => 'Keita', 'signification' => 'Prince d\'or en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Cisse', 'signification' => 'Fils du chef en mandingue', 'groupe_ethnique' => 'Marka', 'langue' => 'Dioula'],
            ['nom' => 'Sinon', 'signification' => 'Celui qui est fort', 'groupe_ethnique' => 'Bobo', 'langue' => 'Bobo'],
            ['nom' => 'Sawadoe', 'signification' => 'Maître tisserand', 'groupe_ethnique' => 'Bobo', 'langue' => 'Bobo'],
            ['nom' => 'Kabore', 'signification' => 'Gouverneur du peuple', 'groupe_ethnique' => 'Bobo', 'langue' => 'Bobo'],
            ['nom' => 'Boade', 'signification' => 'Fils de la maison', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Dokwo', 'signification' => 'Celui qui a la sagesse', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Poorgue', 'signification' => 'Celui qui parle vrai', 'groupe_ethnique' => 'Dagara', 'langue' => 'Dagara'],
            ['nom' => 'Nakonoé', 'signification' => 'Celui qui crée', 'groupe_ethnique' => 'Gourmantché', 'langue' => 'Gourmantché'],
            ['nom' => 'Kabessa', 'signification' => 'Chef de rassemblement', 'groupe_ethnique' => 'Gourmantché', 'langue' => 'Gourmantché'],
        ];


        foreach ($patronymes as $data) {
            $groupeEthnique = $groupesEthniques->where('nom', $data['groupe_ethnique'])->first();
            $langue = $langues->where('nom', $data['langue'])->first();
            $region = $regions->random();

            Patronyme::firstOrCreate(
                ['nom' => $data['nom']],
                [
                    'signification' => $data['signification'],
                    'groupe_ethnique_id' => $groupeEthnique?->id,
                    'langue_id' => $langue?->id,
                    'origine' => 'Burkina Faso',
                    'frequence' => rand(1, 100),
                    'patronyme_sexe' => rand(0, 1) ? 'Mixte' : (rand(0, 1) ? 'Masculin' : 'Féminin'),
                    'is_featured' => rand(0, 10) > 7,
                    'region_id' => $region->id,
                ]
            );
        }

        $this->command->info('✓ 60 patronymes burkinabés de test créés avec succès !');
    }
}
