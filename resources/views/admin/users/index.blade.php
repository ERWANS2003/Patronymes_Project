<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Gestion des Utilisateurs
                </h1>
                <p class="text-gray-600 mt-1">
                    Créez et gérez les utilisateurs de la plateforme
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Nouvel utilisateur
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Actions -->
        <div class="card mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                    Actions rapides
                </h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Créer un utilisateur
                    </a>
                    <a href="{{ route('admin.roles') }}" class="btn btn-outline">
                        <i class="fas fa-users-cog mr-2"></i>Gérer les rôles
                    </a>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Liste des utilisateurs
                </h3>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="badge {{ $user->role === 'admin' ? 'bg-red-600' : ($user->role === 'contributeur' ? 'bg-green-600' : 'bg-gray-600') }} text-white px-2 py-1 rounded-full text-xs">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            @if($user->can_contribute)
                                                <span class="badge bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Peut contribuer</span>
                                            @endif
                                            @if($user->can_manage_roles)
                                                <span class="badge bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Peut gérer les rôles</span>
                                            @endif
                                            @if(!$user->can_contribute && !$user->can_manage_roles)
                                                <span class="text-xs text-gray-500">Aucune permission spéciale</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-eye mr-1"></i>Voir
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline">
                                                <i class="fas fa-edit mr-1"></i>Modifier
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <i class="fas fa-trash mr-1"></i>Supprimer
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
