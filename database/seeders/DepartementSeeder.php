<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departement;
use App\Models\Region;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les régions existantes
        $regions = Region::all()->keyBy('name');

        $departements = [
            // Région du Centre
            'Centre' => [
                ['name' => 'Ouagadougou', 'code' => 'OUA', 'region_id' => $regions['Centre']->id],
                ['name' => 'Kadiogo', 'code' => 'KAD', 'region_id' => $regions['Centre']->id],
            ],

            // Région du Centre-Est
            'Centre-Est' => [
                ['name' => 'Boulgou', 'code' => 'BLG', 'region_id' => $regions['Centre-Est']->id],
                ['name' => 'Kouritenga', 'code' => 'KRT', 'region_id' => $regions['Centre-Est']->id],
            ],

            // Région du Centre-Nord
            'Centre-Nord' => [
                ['name' => 'Bam', 'code' => 'BAM', 'region_id' => $regions['Centre-Nord']->id],
                ['name' => 'Namentenga', 'code' => 'NAM', 'region_id' => $regions['Centre-Nord']->id],
                ['name' => 'Sanmatenga', 'code' => 'SAN', 'region_id' => $regions['Centre-Nord']->id],
            ],

            // Région du Centre-Ouest
            'Centre-Ouest' => [
                ['name' => 'Boulkiemdé', 'code' => 'BKI', 'region_id' => $regions['Centre-Ouest']->id],
                ['name' => 'Sissili', 'code' => 'SIS', 'region_id' => $regions['Centre-Ouest']->id],
                ['name' => 'Ziro', 'code' => 'ZIR', 'region_id' => $regions['Centre-Ouest']->id],
            ],

            // Région du Centre-Sud
            'Centre-Sud' => [
                ['name' => 'Bazèga', 'code' => 'BAZ', 'region_id' => $regions['Centre-Sud']->id],
                ['name' => 'Nahouri', 'code' => 'NAH', 'region_id' => $regions['Centre-Sud']->id],
                ['name' => 'Zoundwéogo', 'code' => 'ZOU', 'region_id' => $regions['Centre-Sud']->id],
            ],

            // Région de l'Est
            'Est' => [
                ['name' => 'Gnagna', 'code' => 'GNA', 'region_id' => $regions['Est']->id],
                ['name' => 'Gourma', 'code' => 'GOU', 'region_id' => $regions['Est']->id],
                ['name' => 'Komondjari', 'code' => 'KMD', 'region_id' => $regions['Est']->id],
                ['name' => 'Kompienga', 'code' => 'KMP', 'region_id' => $regions['Est']->id],
                ['name' => 'Tapoa', 'code' => 'TAP', 'region_id' => $regions['Est']->id],
            ],

            // Région des Hauts-Bassins
            'Hauts-Bassins' => [
                ['name' => 'Houet', 'code' => 'HOU', 'region_id' => $regions['Hauts-Bassins']->id],
                ['name' => 'Kénédougou', 'code' => 'KEN', 'region_id' => $regions['Hauts-Bassins']->id],
                ['name' => 'Tuy', 'code' => 'TUY', 'region_id' => $regions['Hauts-Bassins']->id],
            ],

            // Région du Nord
            'Nord' => [
                ['name' => 'Loroum', 'code' => 'LOR', 'region_id' => $regions['Nord']->id],
                ['name' => 'Passoré', 'code' => 'PAS', 'region_id' => $regions['Nord']->id],
                ['name' => 'Yatenga', 'code' => 'YAT', 'region_id' => $regions['Nord']->id],
                ['name' => 'Zondoma', 'code' => 'ZON', 'region_id' => $regions['Nord']->id],
            ],

            // Région du Plateau-Central
            'Plateau-Central' => [
                ['name' => 'Ganzourgou', 'code' => 'GAN', 'region_id' => $regions['Plateau-Central']->id],
                ['name' => 'Kourwéogo', 'code' => 'KRW', 'region_id' => $regions['Plateau-Central']->id],
                ['name' => 'Oubritenga', 'code' => 'OUB', 'region_id' => $regions['Plateau-Central']->id],
            ],

            // Région du Sahel
            'Sahel' => [
                ['name' => 'Oudalan', 'code' => 'OUD', 'region_id' => $regions['Sahel']->id],
                ['name' => 'Séno', 'code' => 'SEN', 'region_id' => $regions['Sahel']->id],
                ['name' => 'Soum', 'code' => 'SOU', 'region_id' => $regions['Sahel']->id],
                ['name' => 'Yagha', 'code' => 'YAG', 'region_id' => $regions['Sahel']->id],
            ],

            // Région du Sud-Ouest
            'Sud-Ouest' => [
                ['name' => 'Bougouriba', 'code' => 'BGR', 'region_id' => $regions['Sud-Ouest']->id],
                ['name' => 'Ioba', 'code' => 'IOB', 'region_id' => $regions['Sud-Ouest']->id],
                ['name' => 'Noumbiel', 'code' => 'NMB', 'region_id' => $regions['Sud-Ouest']->id],
                ['name' => 'Poni', 'code' => 'PON', 'region_id' => $regions['Sud-Ouest']->id],
            ],

            // Région des Cascades
            'Cascades' => [
                ['name' => 'Comoé', 'code' => 'COM', 'region_id' => $regions['Cascades']->id],
                ['name' => 'Léraba', 'code' => 'LER', 'region_id' => $regions['Cascades']->id],
            ],

            // Région de la Boucle du Mouhoun
            'Boucle du Mouhoun' => [
                ['name' => 'Balé', 'code' => 'BAL', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['name' => 'Banwa', 'code' => 'BNW', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['name' => 'Kossi', 'code' => 'KOS', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['name' => 'Mouhoun', 'code' => 'MOU', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['name' => 'Nayala', 'code' => 'NAY', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['name' => 'Sourou', 'code' => 'SRU', 'region_id' => $regions['Boucle du Mouhoun']->id],
            ],
        ];

        foreach ($departements as $regionName => $departementList) {
            foreach ($departementList as $departementData) {
                Departement::firstOrCreate(
                    ['name' => $departementData['name']],
                    $departementData
                );
            }
        }
    }
}
