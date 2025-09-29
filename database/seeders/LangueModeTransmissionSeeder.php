<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangueModeTransmissionSeeder extends Seeder
{
    public function run(): void
    {
        $langues = [
            ['nom' => 'Mooré', 'ethnie' => 'Mossi'],
            ['nom' => 'Fulfuldé', 'ethnie' => 'Peulh (Fulbé)'],
            ['nom' => 'Gulmancema', 'ethnie' => 'Gourmantché'],
            ['nom' => 'Bobo', 'ethnie' => 'Bobo'],
            ['nom' => 'Lobiri', 'ethnie' => 'Lobi'],
            ['nom' => 'Dagara', 'ethnie' => 'Dagara'],
            ['nom' => 'Senari', 'ethnie' => 'Sénoufo'],
            ['nom' => 'Bwamu', 'ethnie' => 'Bwaba'],
            ['nom' => 'Bissa', 'ethnie' => 'Bissa'],
            ['nom' => 'Tamasheq', 'ethnie' => 'Touareg'],
        ];

        foreach ($langues as $langue) {
            $ethnieId = DB::table('ethnies')->where('nom', $langue['ethnie'])->value('id');

            DB::table('langues')->insert([
                'nom' => $langue['nom'],
                'ethnie_id' => $ethnieId,
                'mode_transmission' => 'Oral (traditionnel)',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
