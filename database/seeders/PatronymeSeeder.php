<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatronymeSeeder extends Seeder
{
    public function run(): void
    {
        // Ce seeder ne crée plus de données fictives
        // Les patronymes seront ajoutés via l'interface utilisateur
        $this->command->info('PatronymeSeeder: Aucune donnée fictive créée. Les patronymes seront ajoutés via l\'interface utilisateur.');
    }
}
