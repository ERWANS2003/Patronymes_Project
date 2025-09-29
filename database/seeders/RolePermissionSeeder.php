<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Chercheur', 'Utilisateur'];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'nom' => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permissions = [
            'voir_patronymes',
            'ajouter_patronymes',
            'modifier_patronymes',
            'supprimer_patronymes',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'nom' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Exemple : lier Admin à toutes les permissions
        $adminId = DB::table('roles')->where('nom', 'Admin')->value('id');
        $permissionIds = DB::table('permissions')->pluck('id');
        foreach ($permissionIds as $permId) {
            DB::table('role_permission')->insert([
                'role_id' => $adminId,
                'permission_id' => $permId,
            ]);
        }
    }
}
