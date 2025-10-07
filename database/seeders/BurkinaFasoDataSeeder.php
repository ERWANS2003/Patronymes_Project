<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Province;
use App\Models\Commune;

class BurkinaFasoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider les tables existantes
        Commune::truncate();
        Province::truncate();
        Region::truncate();

        // Données réelles du Burkina Faso - 13 régions
        $regionsData = [
            ['nom' => 'Boucle du Mouhoun', 'code' => 'BM'],
            ['nom' => 'Cascades', 'code' => 'CA'],
            ['nom' => 'Centre', 'code' => 'CT'],
            ['nom' => 'Centre-Est', 'code' => 'CE'],
            ['nom' => 'Centre-Nord', 'code' => 'CN'],
            ['nom' => 'Centre-Ouest', 'code' => 'CO'],
            ['nom' => 'Centre-Sud', 'code' => 'CS'],
            ['nom' => 'Est', 'code' => 'ES'],
            ['nom' => 'Hauts-Bassins', 'code' => 'HB'],
            ['nom' => 'Nord', 'code' => 'NO'],
            ['nom' => 'Plateau-Central', 'code' => 'PC'],
            ['nom' => 'Sahel', 'code' => 'SA'],
            ['nom' => 'Sud-Ouest', 'code' => 'SO'],
        ];

        foreach ($regionsData as $regionData) {
            Region::create($regionData);
        }

        // Données des provinces par région
        $provincesData = [
            // Boucle du Mouhoun
            ['nom' => 'Balé', 'region' => 'Boucle du Mouhoun'],
            ['nom' => 'Banwa', 'region' => 'Boucle du Mouhoun'],
            ['nom' => 'Kossi', 'region' => 'Boucle du Mouhoun'],
            ['nom' => 'Mouhoun', 'region' => 'Boucle du Mouhoun'],
            ['nom' => 'Nayala', 'region' => 'Boucle du Mouhoun'],
            ['nom' => 'Sourou', 'region' => 'Boucle du Mouhoun'],

            // Cascades
            ['nom' => 'Comoé', 'region' => 'Cascades'],
            ['nom' => 'Léraba', 'region' => 'Cascades'],

            // Centre
            ['nom' => 'Kadiogo', 'region' => 'Centre'],

            // Centre-Est
            ['nom' => 'Boulgou', 'region' => 'Centre-Est'],
            ['nom' => 'Koulpélogo', 'region' => 'Centre-Est'],
            ['nom' => 'Kouritenga', 'region' => 'Centre-Est'],

            // Centre-Nord
            ['nom' => 'Bam', 'region' => 'Centre-Nord'],
            ['nom' => 'Namentenga', 'region' => 'Centre-Nord'],
            ['nom' => 'Sanmatenga', 'region' => 'Centre-Nord'],

            // Centre-Ouest
            ['nom' => 'Boulkiemdé', 'region' => 'Centre-Ouest'],
            ['nom' => 'Sanguié', 'region' => 'Centre-Ouest'],
            ['nom' => 'Sissili', 'region' => 'Centre-Ouest'],
            ['nom' => 'Ziro', 'region' => 'Centre-Ouest'],

            // Centre-Sud
            ['nom' => 'Bazèga', 'region' => 'Centre-Sud'],
            ['nom' => 'Nahouri', 'region' => 'Centre-Sud'],
            ['nom' => 'Zoundwéogo', 'region' => 'Centre-Sud'],

            // Est
            ['nom' => 'Gnagna', 'region' => 'Est'],
            ['nom' => 'Gourma', 'region' => 'Est'],
            ['nom' => 'Komondjari', 'region' => 'Est'],
            ['nom' => 'Kompienga', 'region' => 'Est'],
            ['nom' => 'Tapoa', 'region' => 'Est'],

            // Hauts-Bassins
            ['nom' => 'Houet', 'region' => 'Hauts-Bassins'],
            ['nom' => 'Kénédougou', 'region' => 'Hauts-Bassins'],
            ['nom' => 'Tuy', 'region' => 'Hauts-Bassins'],

            // Nord
            ['nom' => 'Loroum', 'region' => 'Nord'],
            ['nom' => 'Passoré', 'region' => 'Nord'],
            ['nom' => 'Yatenga', 'region' => 'Nord'],
            ['nom' => 'Zondoma', 'region' => 'Nord'],

            // Plateau-Central
            ['nom' => 'Ganzourgou', 'region' => 'Plateau-Central'],
            ['nom' => 'Kourwéogo', 'region' => 'Plateau-Central'],
            ['nom' => 'Oubritenga', 'region' => 'Plateau-Central'],

            // Sahel
            ['nom' => 'Oudalan', 'region' => 'Sahel'],
            ['nom' => 'Séno', 'region' => 'Sahel'],
            ['nom' => 'Soum', 'region' => 'Sahel'],
            ['nom' => 'Yagha', 'region' => 'Sahel'],

            // Sud-Ouest
            ['nom' => 'Bougouriba', 'region' => 'Sud-Ouest'],
            ['nom' => 'Ioba', 'region' => 'Sud-Ouest'],
            ['nom' => 'Noumbiel', 'region' => 'Sud-Ouest'],
            ['nom' => 'Poni', 'region' => 'Sud-Ouest'],
        ];

        // Créer les provinces
        foreach ($provincesData as $provinceData) {
            $region = Region::where('nom', $provinceData['region'])->first();
            if ($region) {
                Province::create([
                    'nom' => $provinceData['nom'],
                    'region_id' => $region->id,
                ]);
            }
        }

        // Données des communes principales par province
        $communesData = [
            // Kadiogo (Centre)
            ['nom' => 'Ouagadougou', 'province' => 'Kadiogo'],
            ['nom' => 'Saaba', 'province' => 'Kadiogo'],
            ['nom' => 'Komsilga', 'province' => 'Kadiogo'],
            ['nom' => 'Pabré', 'province' => 'Kadiogo'],
            ['nom' => 'Dapélogo', 'province' => 'Kadiogo'],
            ['nom' => 'Tanghin-Dassouri', 'province' => 'Kadiogo'],
            ['nom' => 'Loumbila', 'province' => 'Kadiogo'],

            // Boulkiemdé (Centre-Ouest)
            ['nom' => 'Koudougou', 'province' => 'Boulkiemdé'],
            ['nom' => 'Kokologho', 'province' => 'Boulkiemdé'],
            ['nom' => 'Nanoro', 'province' => 'Boulkiemdé'],
            ['nom' => 'Pella', 'province' => 'Boulkiemdé'],
            ['nom' => 'Ramongo', 'province' => 'Boulkiemdé'],
            ['nom' => 'Sabou', 'province' => 'Boulkiemdé'],
            ['nom' => 'Siglé', 'province' => 'Boulkiemdé'],

            // Sanguié (Centre-Ouest)
            ['nom' => 'Réo', 'province' => 'Sanguié'],
            ['nom' => 'Dassa', 'province' => 'Sanguié'],
            ['nom' => 'Didié', 'province' => 'Sanguié'],
            ['nom' => 'Godyr', 'province' => 'Sanguié'],
            ['nom' => 'Kordié', 'province' => 'Sanguié'],
            ['nom' => 'Midebdo', 'province' => 'Sanguié'],
            ['nom' => 'Pa', 'province' => 'Sanguié'],
            ['nom' => 'Pouni', 'province' => 'Sanguié'],
            ['nom' => 'Ténado', 'province' => 'Sanguié'],

            // Houet (Hauts-Bassins)
            ['nom' => 'Bobo-Dioulasso', 'province' => 'Houet'],
            ['nom' => 'Bama', 'province' => 'Houet'],
            ['nom' => 'Karaba', 'province' => 'Houet'],
            ['nom' => 'Péni', 'province' => 'Houet'],
            ['nom' => 'Saponé', 'province' => 'Houet'],
            ['nom' => 'Sou', 'province' => 'Houet'],
            ['nom' => 'Toussiana', 'province' => 'Houet'],

            // Bazèga (Centre-Sud)
            ['nom' => 'Kombissiri', 'province' => 'Bazèga'],
            ['nom' => 'Gaongo', 'province' => 'Bazèga'],
            ['nom' => 'Kayao', 'province' => 'Bazèga'],
            ['nom' => 'Saponé', 'province' => 'Bazèga'],
            ['nom' => 'Toecé', 'province' => 'Bazèga'],

            // Bam (Centre-Nord)
            ['nom' => 'Kongoussi', 'province' => 'Bam'],
            ['nom' => 'Bourzanga', 'province' => 'Bam'],
            ['nom' => 'Guibaré', 'province' => 'Bam'],
            ['nom' => 'Nasséré', 'province' => 'Bam'],
            ['nom' => 'Rollo', 'province' => 'Bam'],
            ['nom' => 'Rouko', 'province' => 'Bam'],
            ['nom' => 'Sabcé', 'province' => 'Bam'],
            ['nom' => 'Tikaré', 'province' => 'Bam'],
            ['nom' => 'Zitenga', 'province' => 'Bam'],

            // Yatenga (Nord)
            ['nom' => 'Ouahigouya', 'province' => 'Yatenga'],
            ['nom' => 'Barga', 'province' => 'Yatenga'],
            ['nom' => 'Kamboincé', 'province' => 'Yatenga'],
            ['nom' => 'Koumbri', 'province' => 'Yatenga'],
            ['nom' => 'Namissiguima', 'province' => 'Yatenga'],
            ['nom' => 'Ouahigouya', 'province' => 'Yatenga'],
            ['nom' => 'Oula', 'province' => 'Yatenga'],
            ['nom' => 'Rambo', 'province' => 'Yatenga'],
            ['nom' => 'Tangaye', 'province' => 'Yatenga'],
            ['nom' => 'Thiou', 'province' => 'Yatenga'],

            // Gourma (Est)
            ['nom' => 'Fada N\'Gourma', 'province' => 'Gourma'],
            ['nom' => 'Diabo', 'province' => 'Gourma'],
            ['nom' => 'Coalla', 'province' => 'Gourma'],
            ['nom' => 'Diapaga', 'province' => 'Gourma'],
            ['nom' => 'Gayeri', 'province' => 'Gourma'],
            ['nom' => 'Madjoari', 'province' => 'Gourma'],
            ['nom' => 'Pama', 'province' => 'Gourma'],
            ['nom' => 'Tibga', 'province' => 'Gourma'],
            ['nom' => 'Yamba', 'province' => 'Gourma'],

            // Mouhoun (Boucle du Mouhoun)
            ['nom' => 'Dédougou', 'province' => 'Mouhoun'],
            ['nom' => 'Bondokuy', 'province' => 'Mouhoun'],
            ['nom' => 'Douroula', 'province' => 'Mouhoun'],
            ['nom' => 'Kona', 'province' => 'Mouhoun'],
            ['nom' => 'Safané', 'province' => 'Mouhoun'],
            ['nom' => 'Tchériba', 'province' => 'Mouhoun'],

            // Kénédougou (Hauts-Bassins)
            ['nom' => 'Orodara', 'province' => 'Kénédougou'],
            ['nom' => 'Banfora', 'province' => 'Kénédougou'],
            ['nom' => 'Kankalaba', 'province' => 'Kénédougou'],
            ['nom' => 'Niangoloko', 'province' => 'Kénédougou'],
            ['nom' => 'Sindou', 'province' => 'Kénédougou'],
            ['nom' => 'Soubakaniédougou', 'province' => 'Kénédougou'],
            ['nom' => 'Tiéfora', 'province' => 'Kénédougou'],

            // Comoé (Cascades)
            ['nom' => 'Banfora', 'province' => 'Comoé'],
            ['nom' => 'Mangodara', 'province' => 'Comoé'],
            ['nom' => 'Moussodougou', 'province' => 'Comoé'],
            ['nom' => 'Niangoloko', 'province' => 'Comoé'],
            ['nom' => 'Ouo', 'province' => 'Comoé'],
            ['nom' => 'Sidéradougou', 'province' => 'Comoé'],
            ['nom' => 'Soubakaniédougou', 'province' => 'Comoé'],
            ['nom' => 'Tiéfora', 'province' => 'Comoé'],

            // Poni (Sud-Ouest)
            ['nom' => 'Gaoua', 'province' => 'Poni'],
            ['nom' => 'Bondigui', 'province' => 'Poni'],
            ['nom' => 'Bouroum-Bouroum', 'province' => 'Poni'],
            ['nom' => 'Dano', 'province' => 'Poni'],
            ['nom' => 'Diébougou', 'province' => 'Poni'],
            ['nom' => 'Kpuéré', 'province' => 'Poni'],
            ['nom' => 'Loropéni', 'province' => 'Poni'],
            ['nom' => 'Malba', 'province' => 'Poni'],
            ['nom' => 'Nako', 'province' => 'Poni'],
            ['nom' => 'Perigban', 'province' => 'Poni'],
            ['nom' => 'Tiéfora', 'province' => 'Poni'],
        ];

        // Créer les communes
        foreach ($communesData as $communeData) {
            $province = Province::where('nom', $communeData['province'])->first();
            if ($province) {
                Commune::create([
                    'nom' => $communeData['nom'],
                    'province_id' => $province->id,
                ]);
            }
        }

        $this->command->info('Données du Burkina Faso créées avec succès !');
        $this->command->info('Régions: ' . Region::count());
        $this->command->info('Provinces: ' . Province::count());
        $this->command->info('Communes: ' . Commune::count());
    }
}
