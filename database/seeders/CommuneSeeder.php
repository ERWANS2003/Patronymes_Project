<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Centre / Kadiogo
            ['region' => 'Centre', 'province' => 'Kadiogo', 'communes' => ['Ouagadougou', 'Komsilga', 'Tanghin-Dassouri']]
        ];

        foreach ($data as $entry) {
            $regionId = DB::table('regions')->where('name', $entry['region'])->value('id');
            if (!$regionId) {
                continue;
            }
            $provinceId = DB::table('provinces')->where('region_id', $regionId)->where('nom', $entry['province'])->value('id');
            if (!$provinceId) {
                continue;
            }
            foreach ($entry['communes'] as $communeNom) {
                $exists = DB::table('communes')->where('province_id', $provinceId)->where('nom', $communeNom)->exists();
                if (!$exists) {
                    DB::table('communes')->insert([
                        'nom' => $communeNom,
                        'province_id' => $provinceId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
