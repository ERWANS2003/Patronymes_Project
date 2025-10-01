<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::updateOrCreate(
            ['email' => 'admin@patronymes.bf'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'can_contribute' => true,
                'can_manage_roles' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Créer un utilisateur contributeur
        User::updateOrCreate(
            ['email' => 'contributeur@patronymes.bf'],
            [
                'name' => 'Contributeur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CONTRIBUTEUR,
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Créer un utilisateur normal
        User::updateOrCreate(
            ['email' => 'user@patronymes.bf'],
            [
                'name' => 'Utilisateur',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'can_contribute' => false,
                'can_manage_roles' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Mettre à jour les utilisateurs existants sans rôle
        User::whereNull('role')->orWhere('role', '')->update([
            'role' => User::ROLE_USER,
            'can_contribute' => false,
            'can_manage_roles' => false,
            'is_active' => true,
        ]);

        $this->command->info('Utilisateurs avec rôles créés/mis à jour avec succès !');
        $this->command->info('Admin: admin@patronymes.bf / password');
        $this->command->info('Contributeur: contributeur@patronymes.bf / password');
        $this->command->info('Utilisateur: user@patronymes.bf / password');
    }
}
