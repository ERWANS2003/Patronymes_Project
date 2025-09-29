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
            CommuneSeeder::class,
            EthnieSeeder::class,
            GroupeEthniqueEthnieSeeder::class,
            LangueModeTransmissionSeeder::class,
            PatronymeSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
