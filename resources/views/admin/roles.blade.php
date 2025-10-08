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
        <!-- Statistiques améliorées -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Utilisateurs</p>
                        <p class="text-3xl font-bold">{{ $users->where('role', 'user')->count() }}</p>
                        <p class="text-blue-200 text-xs">Lecture seule</p>
                    </div>
                    <div class="bg-blue-400 rounded-full p-3">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Contributeurs</p>
                        <p class="text-3xl font-bold">{{ $users->where('role', 'contributeur')->count() }}</p>
                        <p class="text-green-200 text-xs">Peut contribuer</p>
                    </div>
                    <div class="bg-green-400 rounded-full p-3">
                        <i class="fas fa-user-edit text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Administrateurs</p>
                        <p class="text-3xl font-bold">{{ $users->where('role', 'admin')->count() }}</p>
                        <p class="text-purple-200 text-xs">Accès complet</p>
                    </div>
                    <div class="bg-purple-400 rounded-full p-3">
                        <i class="fas fa-crown text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Actifs</p>
                        <p class="text-3xl font-bold">{{ $users->where('can_contribute', true)->count() }}</p>
                        <p class="text-orange-200 text-xs">Peuvent contribuer</p>
                    </div>
                    <div class="bg-orange-400 rounded-full p-3">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Rechercher un utilisateur..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select id="roleFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tous les rôles</option>
                            <option value="user">Utilisateurs</option>
                            <option value="contributeur">Contributeurs</option>
                            <option value="admin">Administrateurs</option>
                        </select>
                        <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="active">Actifs</option>
                            <option value="inactive">Inactifs</option>
                        </select>
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
                                            <!-- Formulaire inline pour modifier le rôle -->
                                            <form method="POST" action="{{ route('admin.roles.update', $user) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex items-center space-x-2">
                                                    <select name="role" class="form-select form-select-sm" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
                                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                                        <option value="contributeur" {{ $user->role === 'contributeur' ? 'selected' : '' }}>Contributeur</option>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                    <div class="flex flex-col space-y-1">
                                                        <label class="text-xs flex items-center">
                                                            <input type="checkbox" name="can_contribute" value="1" {{ $user->can_contribute ? 'checked' : '' }}
                                                                   onchange="this.form.submit()" class="mr-1">
                                                            Contribuer
                                                        </label>
                                                        <label class="text-xs flex items-center">
                                                            <input type="checkbox" name="can_manage_roles" value="1" {{ $user->can_manage_roles ? 'checked' : '' }}
                                                                   onchange="this.form.submit()" class="mr-1">
                                                            Gérer rôles
                                                        </label>
                                                    </div>
                                                </div>
                                            </form>
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

    <script>
        // Fonctionnalités de recherche et filtrage
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const statusFilter = document.getElementById('statusFilter');
            const tableBody = document.getElementById('usersTableBody');
            const rows = Array.from(tableBody.querySelectorAll('tr'));

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedRole = roleFilter.value;
                const selectedStatus = statusFilter.value;

                rows.forEach(row => {
                    const name = row.querySelector('td:first-child').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const role = row.getAttribute('data-role');
                    const status = row.getAttribute('data-status');

                    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesRole = !selectedRole || role === selectedRole;
                    const matchesStatus = !selectedStatus || status === selectedStatus;

                    if (matchesSearch && matchesRole && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            roleFilter.addEventListener('change', filterTable);
            statusFilter.addEventListener('change', filterTable);

            // Fonction de tri
            window.sortTable = function(columnIndex) {
                const table = document.getElementById('usersTable');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                const isAscending = table.getAttribute('data-sort-direction') !== 'asc';
                
                rows.sort((a, b) => {
                    const aText = a.cells[columnIndex].textContent.trim();
                    const bText = b.cells[columnIndex].textContent.trim();
                    
                    if (isAscending) {
                        return aText.localeCompare(bText);
                    } else {
                        return bText.localeCompare(aText);
                    }
                });
                
                rows.forEach(row => tbody.appendChild(row));
                table.setAttribute('data-sort-direction', isAscending ? 'asc' : 'desc');
            };

            // Fonction d'export
            window.exportUsers = function() {
                const table = document.getElementById('usersTable');
                const rows = Array.from(table.querySelectorAll('tr'));
                let csv = '';
                
                rows.forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td, th'));
                    const rowData = cells.map(cell => `"${cell.textContent.trim()}"`).join(',');
                    csv += rowData + '\n';
                });
                
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'utilisateurs.csv';
                a.click();
                window.URL.revokeObjectURL(url);
            };

            // Animation des cartes de statistiques
            const statCards = document.querySelectorAll('.bg-gradient-to-r');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-fade-in');
            });
        });

        // CSS pour les animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fade-in 0.6s ease-out forwards;
            }
            .form-select-sm {
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
            }
        `;
        document.head.appendChild(style);
    </script>

</x-app-layout>
