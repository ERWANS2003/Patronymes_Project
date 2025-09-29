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
            RegionProvinceCommuneSeeder::class,
            ProvinceSeeder::class,
            CommuneSeeder::class,
            DepartementSeeder::class,
            EthnieSeeder::class,
            GroupeEthniqueEthnieSeeder::class,
            LangueModeTransmissionSeeder::class,
            PatronymeSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
