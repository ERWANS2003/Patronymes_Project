<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filtrage par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtrage par statut de contribution
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('can_contribute', true);
            } elseif ($request->status === 'inactive') {
                $query->where('can_contribute', false);
            }
        }

        // Recherche par nom ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(20)->withQueryString();

        // Statistiques pour les cartes
        $stats = [
            'total' => User::count(),
            'users' => User::where('role', 'user')->count(),
            'contributors' => User::where('role', 'contributeur')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'active' => User::where('can_contribute', true)->count(),
        ];

        return view('admin.roles', compact('users', 'stats'));
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
