<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Statistiques du Répertoire
                </h1>
                <p class="text-gray-600 mt-1">
                    Vue d'ensemble des données et performances du système
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('patronymes.index') }}" class="btn btn-outline">
                    <i class="fas fa-list mr-2"></i>Voir les patronymes
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                </a>
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
                    @if(Auth::check() && Auth::user()->canContribute())
                        <a href="{{ route('patronymes.create') }}" class="btn btn-success flex-1">
                            <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Real-time Search Results -->
        <div id="stats-real-time-results" class="hidden">
            <!-- Real-time search results will be displayed here -->
        </div>

        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Patronymes -->
            <div class="bg-gradient-to-r from-bfGreen-600 to-bfGreen-700 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-bfGreen-100 text-sm font-medium">Total Patronymes</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_patronymes'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-bfGreen-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-bfGreen-100 text-sm">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>+12% ce mois</span>
                </div>
            </div>

            <!-- Régions -->
            <div class="bg-gradient-to-r from-bfGold-500 to-bfGold-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-bfGold-100 text-sm font-medium">Régions couvertes</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_regions'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-bfGold-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-bfGold-100 text-sm">
                    <i class="fas fa-check mr-1"></i>
                    <span>Toutes les régions</span>
                </div>
            </div>

            <!-- Utilisateurs -->
            <div class="bg-gradient-to-r from-secondary-600 to-secondary-700 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-100 text-sm font-medium">Utilisateurs actifs</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_users'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-secondary-100 text-sm">
                    <i class="fas fa-user-plus mr-1"></i>
                    <span>+5 nouveaux cette semaine</span>
                </div>
            </div>

            <!-- Favoris -->
            <div class="bg-gradient-to-r from-bfRed-600 to-bfRed-700 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-bfRed-100 text-sm font-medium">Favoris totaux</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_favorites'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-bfRed-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-bfRed-100 text-sm">
                    <i class="fas fa-heart mr-1"></i>
                    <span>Engagement élevé</span>
                </div>
            </div>
        </div>

        <!-- Grille de contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Patronymes les plus consultés -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-eye text-bfGreen-600 mr-2"></i>
                        Patronymes les plus consultés
                    </h3>
                    <span class="text-sm text-gray-500">Top 10</span>
                </div>

                @if(isset($stats['most_viewed']) && $stats['most_viewed']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['most_viewed'] as $index => $patronyme)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-bfGreen-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-bfGreen-700">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h4>
                                    @if($patronyme->region)
                                        <p class="text-sm text-gray-600">{{ $patronyme->region->nom }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-eye mr-1"></i>{{ number_format($patronyme->views_count) }}
                                    </span>
                                    <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary text-sm px-3 py-1">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-eye text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Patronymes récents -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-clock text-bfGold-600 mr-2"></i>
                        Patronymes récents
                    </h3>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-outline text-sm">
                        Voir tout
                    </a>
                </div>

                @if(isset($stats['recent_patronymes']) && $stats['recent_patronymes']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_patronymes'] as $patronyme)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-10 h-10 bg-bfGold-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-book text-bfGold-700"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h4>
                                    @if($patronyme->region)
                                        <p class="text-sm text-gray-600">{{ $patronyme->region->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">{{ $patronyme->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary text-sm px-3 py-1">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Répartition par région -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-map text-bfGold-600 mr-2"></i>
                        Répartition par région
                    </h3>
                    <span class="text-sm text-gray-500">{{ $stats['patronymes_by_region']->count() ?? 0 }} régions</span>
                </div>

                @if(isset($stats['patronymes_by_region']) && $stats['patronymes_by_region']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['patronymes_by_region'] as $region)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-bfGold-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-bfGold-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $region->nom }}</span>
                                </div>
                                <span class="bg-bfGold-100 text-bfGold-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $region->count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-map text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Répartition par groupe ethnique -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-users text-orange-600 mr-2"></i>
                        Répartition par groupe ethnique
                    </h3>
                    <span class="text-sm text-gray-500">{{ $stats['patronymes_by_ethnic_group']->count() ?? 0 }} groupes</span>
                </div>

                @if(isset($stats['patronymes_by_ethnic_group']) && $stats['patronymes_by_ethnic_group']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['patronymes_by_ethnic_group'] as $group)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-orange-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $group->nom }}</span>
                                </div>
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $group->count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Actions rapides
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('patronymes.index') }}" class="btn btn-primary w-full justify-center">
                    <i class="fas fa-search mr-2"></i>Explorer les patronymes
                </a>
                @if(Auth::check() && Auth::user()->canContribute())
                    <a href="{{ route('patronymes.create') }}" class="btn btn-success w-full justify-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                    </a>
                @endif
                <a href="{{ route('favorites.index') }}" class="btn btn-secondary w-full justify-center">
                    <i class="fas fa-heart mr-2"></i>Mes favoris
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline w-full justify-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                </a>
            </div>
        </div>
        </div>
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
                    const realTimeContainer = document.getElementById('stats-real-time-results');
                    if (realTimeContainer) {
                        realTimeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche en cours...</div>';
                        realTimeContainer.classList.remove('hidden');
                    }
                },

                hideRealTimeLoading() {
                    // Loading will be replaced by results
                },

                hideRealTimeResults() {
                    const realTimeContainer = document.getElementById('stats-real-time-results');
                    if (realTimeContainer) {
                        realTimeContainer.classList.add('hidden');
                    }
                },

                displayRealTimeResults(data) {
                    const realTimeContainer = document.getElementById('stats-real-time-results');
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
    </script>
</x-app-layout>
