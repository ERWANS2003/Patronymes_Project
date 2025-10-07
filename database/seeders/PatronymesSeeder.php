<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatronymesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Les régions, provinces et communes sont créées par BurkinaFasoDataSeeder

        // Créer les groupes ethniques
        $groupesEthniques = [
            'Mossi',
            'Peul',
            'Gourmantché',
            'Bissa',
            'Samo',
            'Dagara',
            'Lobi',
            'Bobo',
            'Sénoufo',
            'Turka',
            'Marka',
            'Djerma',
            'Haoussa',
            'Touareg',
            'Yadse',
            'Kurumba',
            'Yana',
            'Fulfuldé',
            'Djerma',
            'Autres'
        ];

        foreach ($groupesEthniques as $nom) {
            \App\Models\GroupeEthnique::firstOrCreate(['nom' => $nom]);
        }

        // Créer les langues
        $langues = [
            'Mooré',
            'Peul',
            'Dioula',
            'Gourmantché',
            'Bissa',
            'Samo',
            'Dagara',
            'Lobi',
            'Bobo',
            'Sénoufo',
            'Marka',
            'Djerma',
            'Haoussa',
            'Tamasheq',
            'Yadse',
            'Kurumba',
            'Yana',
            'Fulfuldé',
            'Français',
            'Autres'
        ];

        foreach ($langues as $nom) {
            \App\Models\Langue::firstOrCreate(['nom' => $nom]);
        }

        // Créer les modes de transmission
        $modesTransmission = [
            ['type' => 'Père en fils/fille', 'description' => 'Transmission patrilinéaire'],
            ['type' => 'Mère en fils/fille', 'description' => 'Transmission matrilinéaire'],
            ['type' => 'Mixte', 'description' => 'Transmission selon les circonstances'],
        ];

        foreach ($modesTransmission as $mode) {
            \App\Models\ModeTransmission::firstOrCreate(
                ['type' => $mode['type']],
                $mode
            );
        }

        // Les provinces et communes sont créées par BurkinaFasoDataSeeder

        // Créer quelques ethnies
        $groupeMossi = \App\Models\GroupeEthnique::where('nom', 'Mossi')->first();
        if ($groupeMossi) {
            $ethnies = [
                'Mossi du Yatenga',
                'Mossi du Tenkodogo',
                'Mossi de Ouagadougou',
                'Mossi de Koupéla',
                'Mossi de Fada'
            ];

            foreach ($ethnies as $nom) {
                \App\Models\Ethnie::firstOrCreate([
                    'nom' => $nom,
                    'groupe_ethnique_id' => $groupeMossi->id
                ]);
            }
        }

        // Créer des patronymes de test
        $patronymes = [
            [
                'nom' => 'Ouédraogo',
                'origine' => 'Origine mossi, signifie "cheval de guerre"',
                'signification' => 'Cheval de guerre, guerrier à cheval',
                'histoire' => 'Patronyme très répandu au Burkina Faso, particulièrement chez les Mossi. Il évoque la tradition guerrière et la noblesse.',
                'region_id' => \App\Models\Region::where('nom', 'Centre')->first()?->id,
                'province_id' => \App\Models\Province::where('nom', 'Kadiogo')->first()?->id,
                'commune_id' => \App\Models\Commune::where('nom', 'Ouagadougou')->first()?->id,
                'groupe_ethnique_id' => \App\Models\GroupeEthnique::where('nom', 'Mossi')->first()?->id,
                'ethnie_id' => \App\Models\Ethnie::where('nom', 'Mossi de Ouagadougou')->first()?->id,
                'langue_id' => \App\Models\Langue::where('nom', 'Mooré')->first()?->id,
                'transmission' => 'pere',
                'frequence' => 95,
                'views_count' => 1250,
                'is_featured' => true,
                'date_collecte' => now(),
                'collecteur' => 'Admin System',
                'code_fiche' => 'PAT001',
                'enquete_nom' => 'Admin Test',
                'enquete_age' => 35,
                'enquete_sexe' => 'M',
                'enquete_fonction' => 'Administrateur'
            ],
            [
                'nom' => 'Traoré',
                'origine' => 'Origine mandé, très répandu en Afrique de l\'Ouest',
                'signification' => 'Lion, courageux',
                'histoire' => 'Patronyme d\'origine mandé, porté par de nombreux groupes ethniques. Symbole de courage et de force.',
                'region_id' => \App\Models\Region::where('nom', 'Hauts-Bassins')->first()?->id,
                'groupe_ethnique_id' => \App\Models\GroupeEthnique::where('nom', 'Marka')->first()?->id,
                'langue_id' => \App\Models\Langue::where('nom', 'Dioula')->first()?->id,
                'transmission' => 'pere',
                'frequence' => 88,
                'views_count' => 980,
                'is_featured' => true,
                'date_collecte' => now(),
                'collecteur' => 'Admin System',
                'code_fiche' => 'PAT002',
                'enquete_nom' => 'Admin Test',
                'enquete_age' => 32,
                'enquete_sexe' => 'M',
                'enquete_fonction' => 'Administrateur'
            ],
            [
                'nom' => 'Sawadogo',
                'origine' => 'Origine mossi, signifie "cheval blanc"',
                'signification' => 'Cheval blanc, pureté',
                'histoire' => 'Patronyme mossi évoquant la pureté et la noblesse. Très présent dans le centre du Burkina Faso.',
                'region_id' => \App\Models\Region::where('nom', 'Centre')->first()?->id,
                'groupe_ethnique_id' => \App\Models\GroupeEthnique::where('nom', 'Mossi')->first()?->id,
                'langue_id' => \App\Models\Langue::where('nom', 'Mooré')->first()?->id,
                'transmission' => 'pere',
                'frequence' => 82,
                'views_count' => 750,
                'is_featured' => true,
                'date_collecte' => now(),
                'collecteur' => 'Admin System',
                'code_fiche' => 'PAT003',
                'enquete_nom' => 'Admin Test',
                'enquete_age' => 28,
                'enquete_sexe' => 'F',
                'enquete_fonction' => 'Administrateur'
            ],
            [
                'nom' => 'Kaboré',
                'origine' => 'Origine mossi, signifie "roi"',
                'signification' => 'Roi, chef, dirigeant',
                'histoire' => 'Patronyme mossi réservé aux familles royales et nobles. Évoque le pouvoir et la sagesse.',
                'region_id' => \App\Models\Region::where('nom', 'Centre')->first()?->id,
                'groupe_ethnique_id' => \App\Models\GroupeEthnique::where('nom', 'Mossi')->first()?->id,
                'langue_id' => \App\Models\Langue::where('nom', 'Mooré')->first()?->id,
                'transmission' => 'pere',
                'frequence' => 78,
                'views_count' => 650,
                'is_featured' => false,
                'date_collecte' => now(),
                'collecteur' => 'Admin System',
                'code_fiche' => 'PAT004',
                'enquete_nom' => 'Admin Test',
                'enquete_age' => 45,
                'enquete_sexe' => 'M',
                'enquete_fonction' => 'Administrateur'
            ],
            [
                'nom' => 'Zongo',
                'origine' => 'Origine peule, signifie "étranger"',
                'signification' => 'Étranger, voyageur',
                'histoire' => 'Patronyme peul évoquant le voyage et l\'ouverture d\'esprit. Porté par de nombreuses familles nomades.',
                'region_id' => \App\Models\Region::where('nom', 'Sahel')->first()?->id,
                'groupe_ethnique_id' => \App\Models\GroupeEthnique::where('nom', 'Peul')->first()?->id,
                'langue_id' => \App\Models\Langue::where('nom', 'Peul')->first()?->id,
                'transmission' => 'pere',
                'frequence' => 65,
                'views_count' => 520,
                'is_featured' => false,
                'date_collecte' => now(),
                'collecteur' => 'Admin System',
                'code_fiche' => 'PAT005',
                'enquete_nom' => 'Admin Test',
                'enquete_age' => 38,
                'enquete_sexe' => 'M',
                'enquete_fonction' => 'Administrateur'
            ]
        ];

        foreach ($patronymes as $patronymeData) {
            \App\Models\Patronyme::firstOrCreate(
                ['nom' => $patronymeData['nom']],
                $patronymeData
            );
        }

        $this->command->info('Seeders exécutés avec succès !');
    }
}
