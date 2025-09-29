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
        // Mettre à jour l'utilisateur admin existant
        $admin = User::where('email', 'admin@patronymes.bf')->first();
        if ($admin) {
            $admin->update([
                'role' => 'admin',
                'can_contribute' => true,
                'can_manage_roles' => true,
            ]);
        }

        // Créer un utilisateur contributeur de test
        User::updateOrCreate(
            ['email' => 'contributeur@patronymes.bf'],
            [
                'name' => 'Contributeur Test',
                'password' => bcrypt('password'),
                'role' => 'contributeur',
                'can_contribute' => true,
                'can_manage_roles' => false,
            ]
        );

        // Créer un utilisateur normal de test
        User::updateOrCreate(
            ['email' => 'user@patronymes.bf'],
            [
                'name' => 'Utilisateur Test',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => false,
                'can_manage_roles' => false,
            ]
        );
    }
}
