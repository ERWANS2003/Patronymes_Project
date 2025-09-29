<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EthnieSeeder extends Seeder
{
    public function run(): void
    {
        $ethnies = [
    ['nom' => 'Mossi', 'groupe' => 'Voltaïques'],
    ['nom' => 'Peulh (Fulbé)', 'groupe' => 'Peulh'],
    ['nom' => 'Gourmantché', 'groupe' => 'Voltaïques'],
    ['nom' => 'Bobo', 'groupe' => 'Mandé'],
    ['nom' => 'Lobi', 'groupe' => 'Mandé'],
    ['nom' => 'Dagara', 'groupe' => 'Mandé'],
    ['nom' => 'Sénoufo', 'groupe' => 'Gurunsi'],
    ['nom' => 'Bwaba', 'groupe' => 'Gurunsi'],
    ['nom' => 'Bissa', 'groupe' => 'Voltaïques'],
    ['nom' => 'Touareg', 'groupe' => 'Touareg'],
];

        foreach ($ethnies as $ethnie) {
            $groupeId = DB::table('groupe_ethniques')->where('nom', $ethnie['groupe'])->value('id');
            if (!$groupeId) {
                $groupeId = DB::table('groupe_ethniques')->insertGetId([
                    'nom' => $ethnie['groupe'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $exists = DB::table('ethnies')
                ->where('nom', $ethnie['nom'])
                ->where('groupe_ethnique_id', $groupeId)
                ->exists();

            if (!$exists) {
                DB::table('ethnies')->insert([
                    'nom' => $ethnie['nom'],
                    'groupe_ethnique_id' => $groupeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
