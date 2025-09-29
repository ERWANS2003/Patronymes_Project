<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.roles', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,contributeur,admin',
            'can_contribute' => 'boolean',
            'can_manage_roles' => 'boolean',
        ]);

        // Seuls les admins peuvent modifier les rôles
        if (!Auth::user()->canManageRoles()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        // Un admin ne peut pas se retirer ses propres permissions d'admin
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle d\'administrateur.');
        }

        $user->update([
            'role' => $request->role,
            'can_contribute' => $request->boolean('can_contribute'),
            'can_manage_roles' => $request->boolean('can_manage_roles'),
        ]);

        return redirect()->back()->with('success', 'Rôle mis à jour avec succès.');
    }

    public function toggleContribution(User $user)
    {
        if (!Auth::user()->canManageRoles()) {
            return redirect()->back()->with('error', 'Vous n\'avez pas les permissions nécessaires.');
        }

        $user->update([
            'can_contribute' => !$user->can_contribute
        ]);

        $status = $user->can_contribute ? 'activé' : 'désactivé';
        return redirect()->back()->with('success', "Permission de contribution {$status} pour {$user->name}.");
    }
}
