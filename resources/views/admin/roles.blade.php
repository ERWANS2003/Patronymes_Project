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
        <!-- Header avec actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestion des rôles</h1>
                <p class="text-gray-600 mt-1">Gérez les permissions et les accès des utilisateurs</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button onclick="refreshData()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Actualiser
                </button>
                <button onclick="exportUsers()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-download mr-2"></i>
                    Exporter
                </button>
            </div>
        </div>

        <!-- Statistiques modernes style Linear -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Utilisateurs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'user')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-edit text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Contributeurs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'contributeur')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-crown text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Administrateurs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'admin')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-orange-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Actifs</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $users->where('can_contribute', true)->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de recherche et filtres modernes -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Recherche -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Rechercher par nom ou email..." 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <!-- Filtres -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <select id="roleFilter" class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les rôles</option>
                            <option value="user">👤 Utilisateurs</option>
                            <option value="contributeur">✏️ Contributeurs</option>
                            <option value="admin">👑 Administrateurs</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <select id="statusFilter" class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="active">🟢 Actifs</option>
                            <option value="inactive">🔴 Inactifs</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Bouton de réinitialisation -->
                    <button onclick="clearFilters()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-times mr-2"></i>
                        Effacer
                    </button>
                </div>
            </div>
            
            <!-- Résultats de recherche -->
            <div id="searchResults" class="mt-4 text-sm text-gray-600 hidden">
                <span id="resultCount">0</span> résultat(s) trouvé(s)
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
                                                <form method="POST" action="{{ route('admin.roles.toggle-contribution', $user) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $user->can_contribute ? 'btn-warning' : 'btn-success' }}">
                                                        <i class="fas fa-{{ $user->can_contribute ? 'ban' : 'check' }} mr-1"></i>
                                                        {{ $user->can_contribute ? 'Désactiver' : 'Activer' }} contribution
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

    <script>
        // Fonctionnalités modernes de gestion des rôles
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const statusFilter = document.getElementById('statusFilter');
            const tableBody = document.getElementById('usersTableBody');
            const searchResults = document.getElementById('searchResults');
            const resultCount = document.getElementById('resultCount');
            const totalUsers = document.getElementById('totalUsers');
            
            let allRows = Array.from(tableBody.querySelectorAll('tr'));

            // Fonction de filtrage améliorée
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedRole = roleFilter.value;
                const selectedStatus = statusFilter.value;
                let visibleCount = 0;

                allRows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';
                    const role = row.getAttribute('data-role');
                    const status = row.getAttribute('data-status');

                    const matchesSearch = !searchTerm || name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesRole = !selectedRole || role === selectedRole;
                    const matchesStatus = !selectedStatus || status === selectedStatus;

                    if (matchesSearch && matchesRole && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Mise à jour du compteur de résultats
                if (searchTerm || selectedRole || selectedStatus) {
                    searchResults.classList.remove('hidden');
                    resultCount.textContent = visibleCount;
                } else {
                    searchResults.classList.add('hidden');
                }
            }

            // Fonction de réinitialisation des filtres
            window.clearFilters = function() {
                searchInput.value = '';
                roleFilter.value = '';
                statusFilter.value = '';
                filterTable();
            };

            // Fonction d'actualisation
            window.refreshData = function() {
                location.reload();
            };

            // Fonction de tri améliorée
            window.sortTable = function(columnIndex) {
                const table = document.getElementById('usersTable');
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                const isAscending = table.getAttribute('data-sort-direction') !== 'asc';
                
                rows.sort((a, b) => {
                    let aText, bText;
                    
                    if (columnIndex === 0) {
                        // Tri par nom
                        aText = a.getAttribute('data-name') || '';
                        bText = b.getAttribute('data-name') || '';
                    } else if (columnIndex === 2) {
                        // Tri par rôle
                        aText = a.getAttribute('data-role') || '';
                        bText = b.getAttribute('data-role') || '';
                    } else {
                        aText = a.cells[columnIndex].textContent.trim();
                        bText = b.cells[columnIndex].textContent.trim();
                    }
                    
                    if (isAscending) {
                        return aText.localeCompare(bText);
                    } else {
                        return bText.localeCompare(aText);
                    }
                });
                
                rows.forEach(row => tbody.appendChild(row));
                table.setAttribute('data-sort-direction', isAscending ? 'asc' : 'desc');
                
                // Mise à jour de la référence des lignes
                allRows = Array.from(tbody.querySelectorAll('tr'));
            };

            // Fonction d'export améliorée
            window.exportUsers = function() {
                const visibleRows = allRows.filter(row => row.style.display !== 'none');
                let csv = 'Nom,Email,Rôle,Peut contribuer,Peut gérer les rôles\n';
                
                visibleRows.forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    if (cells.length >= 4) {
                        const name = cells[0].textContent.trim();
                        const email = cells[1].textContent.trim();
                        const role = cells[2].textContent.trim();
                        const permissions = cells[3].textContent.trim();
                        
                        csv += `"${name}","${email}","${role}","${permissions}"\n`;
                    }
                });
                
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `utilisateurs_${new Date().toISOString().split('T')[0]}.csv`;
                a.click();
                window.URL.revokeObjectURL(url);
            };

            // Event listeners
            searchInput.addEventListener('input', filterTable);
            roleFilter.addEventListener('change', filterTable);
            statusFilter.addEventListener('change', filterTable);

            // Raccourcis clavier
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'f':
                            e.preventDefault();
                            searchInput.focus();
                            break;
                        case 'r':
                            e.preventDefault();
                            refreshData();
                            break;
                        case 'e':
                            e.preventDefault();
                            exportUsers();
                            break;
                    }
                }
            });

            // Animation d'entrée des cartes
            const statCards = document.querySelectorAll('.bg-white.rounded-lg.border');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease-out';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Notifications toast
            window.showToast = function(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-medium transform translate-x-full transition-transform duration-300 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                toast.textContent = message;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                }, 100);
                
                setTimeout(() => {
                    toast.style.transform = 'translateX(full)';
                    setTimeout(() => document.body.removeChild(toast), 300);
                }, 3000);
            };
        });

        // CSS pour les animations et styles modernes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fade-in {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            @keyframes slide-in {
                from { transform: translateX(-100%); }
                to { transform: translateX(0); }
            }
            
            .animate-fade-in {
                animation: fade-in 0.6s ease-out forwards;
            }
            
            .animate-slide-in {
                animation: slide-in 0.3s ease-out forwards;
            }
            
            /* Styles pour les sélecteurs personnalisés */
            select:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
            
            /* Amélioration des transitions */
            * {
                transition: all 0.2s ease-in-out;
            }
            
            /* Styles pour les badges modernes */
            .badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 500;
            }
            
            /* Amélioration des boutons */
            button:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }
        `;
        document.head.appendChild(style);
    </script>

</x-app-layout>
