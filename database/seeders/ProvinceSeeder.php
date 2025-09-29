<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Region;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les régions existantes
        $regions = Region::all()->keyBy('name');

        $provinces = [
            // Région du Centre
            'Centre' => [
                ['nom' => 'Kadiogo', 'region_id' => $regions['Centre']->id],
            ],

            // Région du Centre-Est
            'Centre-Est' => [
                ['nom' => 'Boulgou', 'region_id' => $regions['Centre-Est']->id],
                ['nom' => 'Kouritenga', 'region_id' => $regions['Centre-Est']->id],
            ],

            // Région du Centre-Nord
            'Centre-Nord' => [
                ['nom' => 'Bam', 'region_id' => $regions['Centre-Nord']->id],
                ['nom' => 'Namentenga', 'region_id' => $regions['Centre-Nord']->id],
                ['nom' => 'Sanmatenga', 'region_id' => $regions['Centre-Nord']->id],
            ],

            // Région du Centre-Ouest
            'Centre-Ouest' => [
                ['nom' => 'Boulkiemdé', 'region_id' => $regions['Centre-Ouest']->id],
                ['nom' => 'Sissili', 'region_id' => $regions['Centre-Ouest']->id],
                ['nom' => 'Ziro', 'region_id' => $regions['Centre-Ouest']->id],
            ],

            // Région du Centre-Sud
            'Centre-Sud' => [
                ['nom' => 'Bazèga', 'region_id' => $regions['Centre-Sud']->id],
                ['nom' => 'Nahouri', 'region_id' => $regions['Centre-Sud']->id],
                ['nom' => 'Zoundwéogo', 'region_id' => $regions['Centre-Sud']->id],
            ],

            // Région de l'Est
            'Est' => [
                ['nom' => 'Gnagna', 'region_id' => $regions['Est']->id],
                ['nom' => 'Gourma', 'region_id' => $regions['Est']->id],
                ['nom' => 'Komondjari', 'region_id' => $regions['Est']->id],
                ['nom' => 'Kompienga', 'region_id' => $regions['Est']->id],
                ['nom' => 'Tapoa', 'region_id' => $regions['Est']->id],
            ],

            // Région des Hauts-Bassins
            'Hauts-Bassins' => [
                ['nom' => 'Houet', 'region_id' => $regions['Hauts-Bassins']->id],
                ['nom' => 'Kénédougou', 'region_id' => $regions['Hauts-Bassins']->id],
                ['nom' => 'Tuy', 'region_id' => $regions['Hauts-Bassins']->id],
            ],

            // Région du Nord
            'Nord' => [
                ['nom' => 'Loroum', 'region_id' => $regions['Nord']->id],
                ['nom' => 'Passoré', 'region_id' => $regions['Nord']->id],
                ['nom' => 'Yatenga', 'region_id' => $regions['Nord']->id],
                ['nom' => 'Zondoma', 'region_id' => $regions['Nord']->id],
            ],

            // Région du Plateau-Central
            'Plateau-Central' => [
                ['nom' => 'Ganzourgou', 'region_id' => $regions['Plateau-Central']->id],
                ['nom' => 'Kourwéogo', 'region_id' => $regions['Plateau-Central']->id],
                ['nom' => 'Oubritenga', 'region_id' => $regions['Plateau-Central']->id],
            ],

            // Région du Sahel
            'Sahel' => [
                ['nom' => 'Oudalan', 'region_id' => $regions['Sahel']->id],
                ['nom' => 'Séno', 'region_id' => $regions['Sahel']->id],
                ['nom' => 'Soum', 'region_id' => $regions['Sahel']->id],
                ['nom' => 'Yagha', 'region_id' => $regions['Sahel']->id],
            ],

            // Région du Sud-Ouest
            'Sud-Ouest' => [
                ['nom' => 'Bougouriba', 'region_id' => $regions['Sud-Ouest']->id],
                ['nom' => 'Ioba', 'region_id' => $regions['Sud-Ouest']->id],
                ['nom' => 'Noumbiel', 'region_id' => $regions['Sud-Ouest']->id],
                ['nom' => 'Poni', 'region_id' => $regions['Sud-Ouest']->id],
            ],

            // Région des Cascades
            'Cascades' => [
                ['nom' => 'Comoé', 'region_id' => $regions['Cascades']->id],
                ['nom' => 'Léraba', 'region_id' => $regions['Cascades']->id],
            ],

            // Région de la Boucle du Mouhoun
            'Boucle du Mouhoun' => [
                ['nom' => 'Balé', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['nom' => 'Banwa', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['nom' => 'Kossi', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['nom' => 'Mouhoun', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['nom' => 'Nayala', 'region_id' => $regions['Boucle du Mouhoun']->id],
                ['nom' => 'Sourou', 'region_id' => $regions['Boucle du Mouhoun']->id],
            ],
        ];

        foreach ($provinces as $regionName => $provinceList) {
            foreach ($provinceList as $provinceData) {
                Province::firstOrCreate(
                    ['nom' => $provinceData['nom']],
                    $provinceData
                );
            }
        }
    }
}
