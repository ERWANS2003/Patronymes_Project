<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-users-cog text-purple-600 mr-2"></i>
                    Gestion des Rôles
                </h1>
                <p class="text-gray-600 mt-1">
                    Gérez les rôles et permissions des utilisateurs
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour au dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Roles Info -->
        <div class="card mb-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Rôles disponibles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center space-x-3">
                        <span class="badge bg-gray-600 text-white px-3 py-1 rounded-full">Utilisateur</span>
                        <span class="text-sm text-gray-600">Lecture seule</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="badge bg-green-600 text-white px-3 py-1 rounded-full">Contributeur</span>
                        <span class="text-sm text-gray-600">Peut contribuer</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="badge bg-red-600 text-white px-3 py-1 rounded-full">Admin</span>
                        <span class="text-sm text-gray-600">Accès complet</span>
                    </div>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button type="button" class="btn btn-sm btn-outline" onclick="openEditModal({{ $user->id }})">
                                                <i class="fas fa-edit mr-1"></i>Modifier
                                            </button>
                                            @if($user->id !== Auth::id())
                                                <a href="{{ route('admin.roles.toggle-contribution', $user) }}"
                                                   class="btn btn-sm {{ $user->can_contribute ? 'btn-warning' : 'btn-success' }}">
                                                    <i class="fas fa-{{ $user->can_contribute ? 'ban' : 'check' }} mr-1"></i>
                                                    {{ $user->can_contribute ? 'Désactiver' : 'Activer' }} contribution
                                                </a>
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

    <!-- Edit Role Modal -->
    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Modifier le rôle</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="role" class="form-label">Rôle</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="user">Utilisateur</option>
                                <option value="contributeur">Contributeur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="can_contribute" id="can_contribute" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="can_contribute" class="ml-2 text-sm text-gray-900">Peut contribuer</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="can_manage_roles" id="can_manage_roles" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="can_manage_roles" class="ml-2 text-sm text-gray-900">Peut gérer les rôles</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(userId) {
            // Récupérer les données de l'utilisateur (simulation)
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            // Définir l'action du formulaire
            form.action = `/admin/roles/${userId}`;

            // Afficher le modal
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.classList.add('hidden');
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</x-app-layout>
