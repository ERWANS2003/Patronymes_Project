<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Données géographiques réelles du Burkina Faso
            RegionProvinceCommuneSeeder::class,
            ProvinceSeeder::class,
            CommuneSeeder::class,
            DepartementSeeder::class,
            
            // Données de référence (ethnies, langues, modes de transmission)
            EthnieSeeder::class,
            GroupeEthniqueEthnieSeeder::class,
            LangueModeTransmissionSeeder::class,
            
            // Compte administrateur uniquement
            RoleSeeder::class,
        ]);
    }
}
