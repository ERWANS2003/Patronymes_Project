<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:user,contributeur,admin',
            'can_contribute' => 'boolean',
            'can_manage_roles' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'can_contribute' => $request->boolean('can_contribute'),
            'can_manage_roles' => $request->boolean('can_manage_roles'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} créé avec succès.");
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,contributeur,admin',
            'can_contribute' => 'boolean',
            'can_manage_roles' => 'boolean',
        ]);

        // Un admin ne peut pas se retirer ses propres permissions d'admin
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier votre propre rôle d\'administrateur.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'can_contribute' => $request->boolean('can_contribute'),
            'can_manage_roles' => $request->boolean('can_manage_roles'),
        ]);

        // Si un nouveau mot de passe est fourni
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} mis à jour avec succès.");
    }

    public function destroy(User $user)
    {
        // Un admin ne peut pas se supprimer lui-même
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$userName} supprimé avec succès.");
    }
}
