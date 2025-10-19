<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-tachometer-alt text-bfGreen-600 mr-2"></i>
                    @if(Auth::user()->role === 'admin')
                        Tableau de bord Administrateur
                    @elseif(Auth::check() && Auth::user()->canContribute())
                        Tableau de bord Contributeur
                    @else
                        Tableau de bord Utilisateur
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">
                    Bienvenue, {{ Auth::user()->name }} !
                    @if(Auth::user()->role === 'admin')
                        Voici l'aperçu complet du système.
                    @elseif(Auth::check() && Auth::user()->canContribute())
                        Voici vos contributions et l'aperçu de votre activité.
                    @else
                        Voici un aperçu de votre activité.
                    @endif
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex items-center space-x-4">
                    <button
                        id="refresh-stats-btn"
                        onclick="refreshStats()"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-bfGreen-600 hover:bg-bfGreen-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bfGreen-500 transition-colors duration-200"
                        title="Rafraîchir les statistiques">
                        <i class="fas fa-sync-alt mr-2" id="refresh-icon"></i>
                        <span id="refresh-text">Actualiser</span>
                    </button>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if(Auth::user()->role === 'admin') bg-bfRed-100 text-bfRed-800
                        @elseif(Auth::check() && Auth::user()->canContribute()) bg-bfGreen-100 text-bfGreen-800
                        @else bg-bfGold-100 text-bfGold-800
                        @endif">
                        @if(Auth::user()->role === 'admin')
                            <i class="fas fa-crown mr-1"></i>Administrateur
                        @elseif(Auth::check() && Auth::user()->canContribute())
                            <i class="fas fa-edit mr-1"></i>Contributeur
                        @else
                            <i class="fas fa-user mr-1"></i>Utilisateur
                        @endif
                    </span>
                    <span class="text-sm text-gray-500">
                        Dernière connexion: {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'Jamais' }}
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('patronymes.index') }}" class="space-y-4">
                <div>
                    <label class="form-label">Recherche rapide</label>
                    <div class="relative" x-data="searchAutocomplete()">
                        <input
                            type="text"
                            name="search"
                            placeholder="Tapez un nom de patronyme... (ex: 'e' pour voir tous les patronymes commençant par 'e')"
                            class="form-input pl-10"
                            x-model="searchQuery"
                            @input="getSuggestions($event.target.value)"
                            @focus="showSuggestions = true"
                            @blur="setTimeout(() => showSuggestions = false, 200)"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>

                        <!-- Suggestions Dropdown -->
                        <div x-show="showSuggestions && suggestions.length > 0"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="suggestion in suggestions" :key="suggestion.value">
                                <div @click="selectSuggestion(suggestion)"
                                     class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900" x-text="suggestion.label"></div>
                                            <div class="text-sm text-gray-500" x-text="suggestion.description" x-show="suggestion.description"></div>
                                        </div>
                                        <div class="text-xs text-gray-400" x-text="suggestion.type"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-secondary flex-1">
                        <i class="fas fa-list mr-2"></i>Voir tous les patronymes
                    </a>
                    <a href="{{ route('patronymes.create') }}" class="btn btn-success flex-1">
                        <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                    </a>
                </div>
            </form>
        </div>

        <!-- Real-time Search Results -->
        <div id="dashboard-real-time-results" class="hidden">
            <!-- Real-time search results will be displayed here -->
        </div>

        <!-- Role-specific Quick Actions -->
        @if(Auth::user()->role === 'admin')
            <div class="bg-bfRed-50 border border-bfRed-200 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-bfRed-800 mb-4">
                    <i class="fas fa-crown mr-2"></i>Actions Administrateur
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-red">
                        <i class="fas fa-cog mr-2"></i>Administration
                    </a>
                    <a href="{{ route('patronymes.create') }}" class="btn btn-green">
                        <i class="fas fa-plus mr-2"></i>Ajouter Patronyme
                    </a>
                    <a href="{{ route('statistics.index') }}" class="btn btn-gold">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                </div>
            </div>
                        @elseif(Auth::check() && Auth::user()->canContribute())
            <div class="bg-bfGreen-50 border border-bfGreen-200 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-bfGreen-800 mb-4">
                    <i class="fas fa-edit mr-2"></i>Actions Contributeur
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('patronymes.create') }}" class="btn btn-green">
                        <i class="fas fa-plus mr-2"></i>Ajouter Patronyme
                    </a>
                    <a href="{{ route('favorites.index') }}" class="btn btn-red">
                        <i class="fas fa-heart mr-2"></i>Mes Favoris
                    </a>
                    <a href="{{ route('statistics.index') }}" class="btn btn-gold">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                </div>
            </div>
        @else
            <div class="bg-bfGold-50 border border-bfGold-200 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-bfGold-800 mb-4">
                    <i class="fas fa-user mr-2"></i>Actions Utilisateur
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('patronymes.index') }}" class="btn btn-blue">
                        <i class="fas fa-search mr-2"></i>Explorer
                    </a>
                    <a href="{{ route('favorites.index') }}" class="btn btn-red">
                        <i class="fas fa-heart mr-2"></i>Mes Favoris
                    </a>
                    <a href="{{ route('statistics.index') }}" class="btn btn-purple">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Patronymes -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-bfGreen-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-2xl text-bfGreen-700"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Patronymes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_patronymes'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mes Favoris -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-bfRed-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-2xl text-bfRed-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Mes Favoris</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_favorites'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recherches -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-bfGold-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search text-2xl text-bfGold-700"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Recherches</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_searches'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributions -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-bfGreen-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-2xl text-bfGreen-700"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Contributions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_contributions'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Patronymes -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">
                                <i class="fas fa-clock text-bfGold-600 mr-2"></i>
                                Patronymes récents
                            </h2>
                            <a href="{{ route('patronymes.index') }}" class="btn btn-outline text-sm">
                                Voir tout
                            </a>
                        </div>

                        @if(isset($recentPatronymes) && $recentPatronymes->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentPatronymes as $patronyme)
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="w-10 h-10 bg-bfGold-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-book text-bfGold-700"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h3>
                                            <p class="text-sm text-gray-600">
                                                @if($patronyme->groupeEthnique)
                                                    {{ $patronyme->groupeEthnique->nom }}
                                                @endif
                                                @if($patronyme->full_location)
                                                    • {{ $patronyme->full_location }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-eye mr-1"></i>{{ $patronyme->views_count }}
                                            </span>
                                            <a href="{{ route('patronymes.show', $patronyme) }}"
                                               class="btn btn-primary text-sm px-3 py-1">
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Aucun patronyme récent</p>
                                <a href="{{ route('patronymes.index') }}" class="btn btn-primary mt-4">
                                    Explorer les patronymes
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-bolt text-bfGold-600 mr-2"></i>
                            Actions rapides
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('patronymes.index') }}" class="btn btn-primary w-full justify-start">
                                <i class="fas fa-search mr-2"></i>Explorer les patronymes
                            </a>
                            <a href="{{ route('patronymes.index') }}?featured=1" class="btn btn-secondary w-full justify-start">
                                <i class="fas fa-star mr-2"></i>Patronymes populaires
                            </a>
                            @auth
                                <a href="{{ route('patronymes.index') }}?favorites=1" class="btn btn-secondary w-full justify-start">
                                    <i class="fas fa-heart mr-2"></i>Mes favoris
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Popular Patronymes -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-fire text-bfRed-600 mr-2"></i>
                            Patronymes populaires
                        </h3>

                        @if(isset($popularPatronymes) && $popularPatronymes->count() > 0)
                            <div class="space-y-3">
                                @foreach($popularPatronymes as $patronyme)
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-bfRed-700">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $patronyme->nom }}</h4>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-eye mr-1"></i>{{ $patronyme->views_count }} vues
                                            </p>
                                        </div>
                                        <a href="{{ route('patronymes.show', $patronyme) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucun patronyme populaire</p>
                        @endif
                    </div>
                </div>

                <!-- Statistics Summary -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-bar text-bfGreen-600 mr-2"></i>
                            Statistiques
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Régions couvertes</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_regions'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Groupes ethniques</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_groupes'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Langues</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_langues'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Utilisateurs actifs</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @auth
            <div class="mt-8">
                <div class="card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-history text-bfGreen-600 mr-2"></i>
                            Activité récente
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-bfGreen-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt text-bfGreen-700"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">Vous vous êtes connecté</p>
                                    <p class="text-xs text-gray-500">{{ now()->diffForHumans() }}</p>
                                </div>
                            </div>

                            @if($stats['my_favorites'] > 0)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-bfRed-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-heart text-bfRed-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">Vous avez {{ $stats['my_favorites'] }} patronyme{{ $stats['my_favorites'] > 1 ? 's' : '' }} en favori</p>
                                        <p class="text-xs text-gray-500">Dernière mise à jour: {{ now()->subHours(2)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>

    <!-- Search Autocomplete JavaScript -->
    <script>
        // Search Autocomplete Function
        function searchAutocomplete() {
            return {
                searchQuery: '',
                suggestions: [],
                showSuggestions: false,
                debounceTimer: null,

                getSuggestions(query) {
                    if (query.length < 2) {
                        this.suggestions = [];
                        this.hideRealTimeResults();
                        return;
                    }

                    // Debounce the request
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => {
                        fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                this.suggestions = data;
                            })
                            .catch(error => {
                                console.error('Error fetching suggestions:', error);
                                this.suggestions = [];
                            });
                    }, 300);

                    // Also perform real-time search
                    this.performRealTimeSearch(query);
                },

                selectSuggestion(suggestion) {
                    this.searchQuery = suggestion.value;
                    this.showSuggestions = false;

                    // Update the form input
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = suggestion.value;
                    }
                },

                // Real-time search functionality
                performRealTimeSearch(query) {
                    console.log('Performing real-time search for:', query);

                    if (query.length < 2) {
                        this.hideRealTimeResults();
                        return;
                    }

                    this.showRealTimeLoading();

                    // Debounce the request
                    clearTimeout(this.realTimeDebounceTimer);
                    this.realTimeDebounceTimer = setTimeout(() => {
                        // Normalize search query for better matching
                        const normalizedQuery = this.normalizeSearchQuery(query);

                        fetch('/patronymes?search=' + encodeURIComponent(normalizedQuery) + '&ajax=1', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => {
                                console.log('Real-time search response status:', response.status);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Real-time search results:', data);
                                this.displayRealTimeResults(data);
                                this.hideRealTimeLoading();
                            })
                            .catch(error => {
                                console.error('Error fetching real-time results:', error);
                                this.hideRealTimeResults();
                                this.hideRealTimeLoading();
                            });
                    }, 500);
                },

                // Normalize search query for better matching
                normalizeSearchQuery(query) {
                    // Common accent mappings for patronymes
                    const accentMap = {
                        'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a',
                        'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
                        'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
                        'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o',
                        'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u',
                        'ý': 'y', 'ÿ': 'y',
                        'ñ': 'n',
                        'ç': 'c'
                    };

                    let normalized = query.toLowerCase();

                    // Replace accents
                    for (const [accented, normal] of Object.entries(accentMap)) {
                        normalized = normalized.replace(new RegExp(accented, 'g'), normal);
                    }

                    console.log('Normalized query:', query, '->', normalized);
                    return normalized;
                },

                // Real-time search functions
                showRealTimeLoading() {
                    const realTimeContainer = document.getElementById('dashboard-real-time-results');
                    if (realTimeContainer) {
                        realTimeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche en cours...</div>';
                        realTimeContainer.classList.remove('hidden');
                    }
                },

                hideRealTimeLoading() {
                    // Loading will be replaced by results
                },

                hideRealTimeResults() {
                    const realTimeContainer = document.getElementById('dashboard-real-time-results');
                    if (realTimeContainer) {
                        realTimeContainer.classList.add('hidden');
                    }
                },

                displayRealTimeResults(data) {
                    const realTimeContainer = document.getElementById('dashboard-real-time-results');
                    if (!realTimeContainer) {
                        console.error('Real-time results container not found');
                        return;
                    }

                    if (!data.patronymes || data.patronymes.length === 0) {
                        realTimeContainer.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-search text-4xl mb-4"></i><p>Aucun patronyme trouvé pour cette recherche.</p></div>';
                        realTimeContainer.classList.remove('hidden');
                        return;
                    }

                    // Create HTML for real-time results
                    const html = `
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                                Résultats en temps réel (${data.patronymes.length} trouvé${data.patronymes.length > 1 ? 's' : ''})
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                ${data.patronymes.map(patronyme => `
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-lg text-gray-900">${patronyme.nom}</h4>
                                            <button onclick="toggleFavorite(${patronyme.id})"
                                                    class="text-gray-400 hover:text-red-500 transition-colors"
                                                    data-patronyme-id="${patronyme.id}">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        </div>
                                        ${patronyme.signification ? `<p class="text-gray-600 text-sm mb-2">${patronyme.signification}</p>` : ''}
                                        ${patronyme.region ? `<p class="text-blue-600 text-xs"><i class="fas fa-map-marker-alt mr-1"></i>${patronyme.region.nom}</p>` : ''}
                                        <div class="flex items-center justify-between mt-3">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-eye mr-1"></i>${patronyme.views_count || 0} vues
                                            </span>
                                            <a href="/patronymes/${patronyme.id}"
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Voir détails <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="mt-4 text-center">
                                <button onclick="window.Alpine.store('searchAutocomplete').hideRealTimeResults()"
                                        class="text-gray-500 hover:text-gray-700 text-sm">
                                    <i class="fas fa-times mr-1"></i>Masquer les résultats en temps réel
                                </button>
                            </div>
                        </div>
                    `;

                    realTimeContainer.innerHTML = html;
                    realTimeContainer.classList.remove('hidden');
                }
            }
        }

        // Fonction pour rafraîchir les statistiques
        function refreshStats() {
            const btn = document.getElementById('refresh-stats-btn');
            const icon = document.getElementById('refresh-icon');
            const text = document.getElementById('refresh-text');

            // Désactiver le bouton et montrer l'animation
            btn.disabled = true;
            icon.classList.add('fa-spin');
            text.textContent = 'Actualisation...';

            // Envoyer la requête AJAX
            fetch('{{ route("dashboard.refresh-stats") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour afficher les nouvelles statistiques
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error('Erreur lors du rafraîchissement');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver le bouton en cas d'erreur
                btn.disabled = false;
                icon.classList.remove('fa-spin');
                text.textContent = 'Actualiser';

                // Afficher un message d'erreur
                alert('Erreur lors du rafraîchissement des statistiques. Veuillez réessayer.');
            });
        }
    </script>
</x-app-layout>
