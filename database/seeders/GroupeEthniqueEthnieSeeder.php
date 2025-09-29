<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupeEthniqueEthnieSeeder extends Seeder
{
    public function run(): void
    {
        $groupes = [
            'Voltaïques' => ['Mossi', 'Gourmantché', 'Bissa'],
            'Mandé' => ['Bobo', 'Lobi', 'Dagara'],
            'Peulh' => ['Peulh (Fulbé)'],
            'Gurunsi' => ['Sénoufo', 'Bwaba'],
            'Touareg' => ['Touareg'],
        ];

        foreach ($groupes as $groupe => $ethnies) {
            $groupeId = DB::table('groupe_ethniques')->where('nom', $groupe)->value('id');
            if (!$groupeId) {
                $groupeId = DB::table('groupe_ethniques')->insertGetId([
                    'nom' => $groupe,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($ethnies as $ethnie) {
                $ethnieId = DB::table('ethnies')->where('nom', $ethnie)->value('id');
                if ($ethnieId) {
                    $exists = DB::table('ethnie_groupe_ethnique')
                        ->where('groupe_ethnique_id', $groupeId)
                        ->where('ethnie_id', $ethnieId)
                        ->exists();
                    if (!$exists) {
                        DB::table('ethnie_groupe_ethnique')->insert([
                            'groupe_ethnique_id' => $groupeId,
                            'ethnie_id' => $ethnieId,
                        ]);
                    }
                }
            }
        }
    }
}
