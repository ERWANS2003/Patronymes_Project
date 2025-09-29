<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer uniquement le compte administrateur principal
        User::firstOrCreate(
            ['email' => 'admin@patronymes.bf'],
            [
                'name' => 'Administrateur',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
                'can_contribute' => true,
                'can_manage_roles' => true,
            ]
        );

        $this->command->info('RoleSeeder: Compte administrateur créé. Aucun compte de test créé.');
    }
}
