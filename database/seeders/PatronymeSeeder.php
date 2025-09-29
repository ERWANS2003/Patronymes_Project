<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatronymeSeeder extends Seeder
{
    public function run(): void
    {
        $patronymes = [
            ['nom' => 'Ouédraogo', 'ethnie' => 'Mossi', 'region' => 'Centre'],
            ['nom' => 'Sawadogo', 'ethnie' => 'Mossi', 'region' => 'Centre-Nord'],
            ['nom' => 'Zongo', 'ethnie' => 'Peulh (Fulbé)', 'region' => 'Hauts-Bassins'],
            ['nom' => 'Bationo', 'ethnie' => 'Bobo', 'region' => 'Hauts-Bassins'],
            ['nom' => 'Kaboré', 'ethnie' => 'Mossi', 'region' => 'Plateau-Central'],
            ['nom' => 'Diarra', 'ethnie' => 'Sénoufo', 'region' => 'Cascades'],
            ['nom' => 'Konaté', 'ethnie' => 'Dagara', 'region' => 'Sud-Ouest'],
        ];

        foreach ($patronymes as $patronyme) {
            $regionId = DB::table('regions')->where('name', $patronyme['region'])->value('id');

            $exists = DB::table('patronymes')
                ->where('nom', $patronyme['nom'])
                ->where('region_id', $regionId)
                ->exists();

            if (!$exists) {
                DB::table('patronymes')->insert([
                    'nom' => $patronyme['nom'],
                    'region_id' => $regionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
