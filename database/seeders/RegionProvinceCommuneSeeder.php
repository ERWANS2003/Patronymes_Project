<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionProvinceCommuneSeeder extends Seeder
{
    public function run()
    {
        // Structure des régions avec leurs codes et leurs provinces
        $regions = [
            ['name' => 'Boucle du Mouhoun', 'code' => '01', 'provinces' => ['Kossi', 'Banwa', 'Nayala', 'Mouhoun', 'Sourou']],
            ['name' => 'Cascades', 'code' => '02', 'provinces' => ['Comoé', 'Léraba']],
            ['name' => 'Centre', 'code' => '03', 'provinces' => ['Kadiogo']],
            ['name' => 'Centre-Est', 'code' => '04', 'provinces' => ['Boulgou', 'Koulpélogo', 'Kouritenga']],
            ['name' => 'Centre-Nord', 'code' => '05', 'provinces' => ['Bam', 'Namentenga', 'Sanmatenga']],
            ['name' => 'Centre-Ouest', 'code' => '06', 'provinces' => ['Boulkiemdé', 'Sanguié', 'Ziro', 'Zondoma']],
            ['name' => 'Centre-Sud', 'code' => '07', 'provinces' => ['Bazèga', 'Nahouri', 'Zoundwéogo']],
            ['name' => 'Est', 'code' => '08', 'provinces' => ['Gnagna', 'Gourma', 'Komondjari', 'Kompienga', 'Tapoa']],
            ['name' => 'Hauts-Bassins', 'code' => '09', 'provinces' => ['Houet', 'Kénédougou', 'Tuy']],
            ['name' => 'Nord', 'code' => '10', 'provinces' => ['Loroum', 'Passoré', 'Yatenga', 'Zondoma']],
            ['name' => 'Plateau-Central', 'code' => '11', 'provinces' => ['Ganzourgou', 'Kourwéogo', 'Oubritenga']],
            ['name' => 'Sahel', 'code' => '12', 'provinces' => ['Oudalan', 'Séno', 'Soum', 'Yagha']],
            ['name' => 'Sud-Ouest', 'code' => '13', 'provinces' => ['Bougouriba', 'Ioba', 'Noumbiel', 'Poni']],
        ];

        foreach ($regions as $region) {
            // Insérer ou récupérer la région par code (idempotent)
            $regionId = DB::table('regions')->where('code', $region['code'])->value('id');
            if (!$regionId) {
                $regionId = DB::table('regions')->insertGetId([
                    'name' => $region['name'],
                    'code' => $region['code'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('regions')->where('id', $regionId)->update([
                    'name' => $region['name'],
                    'updated_at' => now(),
                ]);
            }

            // Insertion des provinces (idempotent par (nom, region_id))
            foreach ($region['provinces'] as $provinceName) {
                $exists = DB::table('provinces')
                    ->where('region_id', $regionId)
                    ->where('nom', $provinceName)
                    ->exists();
                if (!$exists) {
                    DB::table('provinces')->insert([
                        'nom' => $provinceName,
                        'region_id' => $regionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
