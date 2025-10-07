<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test avec différents rôles

        // 1. Administrateur principal
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@patronymes.bf'],
            [
                'name' => 'Administrateur Principal',
                'email' => 'admin@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'can_contribute' => true,
                'can_manage_roles' => true,
                'is_active' => true,
                'login_count' => 25,
                'last_login_at' => now()->subHours(2),
                'email_verified_at' => now(),
            ]
        );

        // 2. Contributeur expert
        \App\Models\User::firstOrCreate(
            ['email' => 'contributeur@patronymes.bf'],
            [
                'name' => 'Dr. Mamadou Sawadogo',
                'email' => 'contributeur@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'contributeur',
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 18,
                'last_login_at' => now()->subHours(5),
                'email_verified_at' => now(),
            ]
        );

        // 3. Utilisateur régulier
        \App\Models\User::firstOrCreate(
            ['email' => 'user@patronymes.bf'],
            [
                'name' => 'Fatoumata Traoré',
                'email' => 'user@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => false,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 12,
                'last_login_at' => now()->subHours(1),
                'email_verified_at' => now(),
            ]
        );

        // 4. Chercheur en anthropologie
        \App\Models\User::firstOrCreate(
            ['email' => 'chercheur@patronymes.bf'],
            [
                'name' => 'Pr. Ibrahim Ouédraogo',
                'email' => 'chercheur@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'contributeur',
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 35,
                'last_login_at' => now()->subMinutes(30),
                'email_verified_at' => now(),
            ]
        );

        // 5. Étudiant en linguistique
        \App\Models\User::firstOrCreate(
            ['email' => 'etudiant@patronymes.bf'],
            [
                'name' => 'Aïcha Kaboré',
                'email' => 'etudiant@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 8,
                'last_login_at' => now()->subDays(1),
                'email_verified_at' => now(),
            ]
        );

        // 6. Archiviste
        \App\Models\User::firstOrCreate(
            ['email' => 'archiviste@patronymes.bf'],
            [
                'name' => 'Boubacar Zongo',
                'email' => 'archiviste@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'contributeur',
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 42,
                'last_login_at' => now()->subHours(12),
                'email_verified_at' => now(),
            ]
        );

        // 7. Journaliste culturel
        \App\Models\User::firstOrCreate(
            ['email' => 'journaliste@patronymes.bf'],
            [
                'name' => 'Mariam Compaoré',
                'email' => 'journaliste@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => true,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 15,
                'last_login_at' => now()->subDays(2),
                'email_verified_at' => now(),
            ]
        );

        // 8. Utilisateur inactif
        \App\Models\User::firstOrCreate(
            ['email' => 'inactif@patronymes.bf'],
            [
                'name' => 'Seydou Koné',
                'email' => 'inactif@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => false,
                'can_manage_roles' => false,
                'is_active' => false,
                'login_count' => 3,
                'last_login_at' => now()->subMonths(2),
                'email_verified_at' => null,
            ]
        );

        // 9. Modérateur
        \App\Models\User::firstOrCreate(
            ['email' => 'moderateur@patronymes.bf'],
            [
                'name' => 'Halima Sangaré',
                'email' => 'moderateur@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'contributeur',
                'can_contribute' => true,
                'can_manage_roles' => true,
                'is_active' => true,
                'login_count' => 28,
                'last_login_at' => now()->subHours(6),
                'email_verified_at' => now(),
            ]
        );

        // 10. Utilisateur test simple
        \App\Models\User::firstOrCreate(
            ['email' => 'test@patronymes.bf'],
            [
                'name' => 'Test User',
                'email' => 'test@patronymes.bf',
                'password' => bcrypt('password'),
                'role' => 'user',
                'can_contribute' => false,
                'can_manage_roles' => false,
                'is_active' => true,
                'login_count' => 1,
                'last_login_at' => now()->subMinutes(10),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Identifiants de connexion :');
        $this->command->info('- Admin: admin@patronymes.bf / password');
        $this->command->info('- Contributeur: contributeur@patronymes.bf / password');
        $this->command->info('- Utilisateur: user@patronymes.bf / password');
        $this->command->info('- Chercheur: chercheur@patronymes.bf / password');
        $this->command->info('- Étudiant: etudiant@patronymes.bf / password');
        $this->command->info('- Archiviste: archiviste@patronymes.bf / password');
        $this->command->info('- Journaliste: journaliste@patronymes.bf / password');
        $this->command->info('- Modérateur: moderateur@patronymes.bf / password');
        $this->command->info('- Test: test@patronymes.bf / password');
    }
}
