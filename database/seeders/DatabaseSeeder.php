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
            // Données administratives du Burkina Faso
            BurkinaFasoDataSeeder::class,
            // Données de base pour les patronymes
            PatronymesSeeder::class,
            // Patronymes réels du Burkina Faso
            RealPatronymesSeeder::class,
            // Utilisateurs de test
            UsersSeeder::class,
        ]);
    }
}
