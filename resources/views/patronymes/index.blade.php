<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                @if(request('featured'))
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Patronymes Populaires
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Découvrez les patronymes les plus consultés et les plus populaires
                    </p>
                @else
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-search text-blue-600 mr-2"></i>
                        Répertoire des Patronymes
                    </h1>
                    <p class="text-gray-600 mt-1">
                        Découvrez l'origine et la signification des noms de famille du Burkina Faso
                    </p>
                @endif
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">
                    {{ $patronymes->total() }} patronyme{{ $patronymes->total() > 1 ? 's' : '' }} trouvé{{ $patronymes->total() > 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Simple Search -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('patronymes.index') }}" class="space-y-6">
                @if(request('featured'))
                    <input type="hidden" name="featured" value="1">
                @endif
                <!-- Optimized Search Input with Suggestions -->
                <div>
                    <label class="form-label">Recherche</label>
                    <div class="relative" id="search-container">
                        <input
                            type="text"
                            name="search"
                            id="search-input"
                            placeholder="Tapez un nom de patronyme... (ex: 'Traoré', 'Ouédraogo')"
                            class="form-input pl-10"
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>

                        <!-- Loading indicator -->
                        <div id="loading-indicator" class="absolute inset-y-0 right-0 pr-3 items-center hidden">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                        </div>

                        <!-- Suggestions Dropdown -->
                        <div id="suggestions-dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                            <!-- Suggestions will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    <a href="{{ route('patronymes.search.advanced') }}" class="btn btn-outline flex-1 sm:flex-none">
                        <i class="fas fa-search-plus mr-2"></i>Recherche avancée
                    </a>
                    @if(request('featured'))
                        <a href="{{ route('patronymes.index') }}" class="btn btn-secondary flex-1 sm:flex-none">
                            <i class="fas fa-list mr-2"></i>Tous les patronymes
                        </a>
                    @else
                        <a href="{{ route('patronymes.index') }}" class="btn btn-secondary flex-1 sm:flex-none">
                            <i class="fas fa-times mr-2"></i>Effacer
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Real-time Search Results -->
        <div id="real-time-results" class="hidden">
            <!-- Real-time search results will be displayed here -->
        </div>

        <!-- Results -->
        @if($patronymes->count() > 0)

            <!-- Patronymes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($patronymes as $patronyme)
                    <div class="card card-hover">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="mb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">
                                            {{ $patronyme->nom }}
                                        </h3>
                                        @if($patronyme->groupeEthnique)
                                            <span class="badge badge-primary">
                                                {{ $patronyme->groupeEthnique->nom }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="ml-2 flex items-center space-x-2">
                                        @if(request('featured') && ($patronyme->is_featured || $patronyme->views_count > 0))
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                                                @if($patronyme->views_count > 0)
                                                    <span class="text-xs text-gray-500">{{ $patronyme->views_count }} vues</span>
                                                @endif
                                            </div>
                                        @endif
                                        @auth
                                            <button class="text-gray-400 hover:text-red-500 transition-colors favorite-btn"
                                                    data-patronyme-id="{{ $patronyme->id }}"
                                                    onclick="toggleFavorite({{ $patronyme->id }})">
                                                <i class="fas fa-heart {{ $patronyme->isFavoritedBy(Auth::id()) ? 'text-red-500' : '' }}"></i>
                                            </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="space-y-3">
                                @if($patronyme->signification)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Signification</h4>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $patronyme->signification }}</p>
                                    </div>
                                @endif

                                @if($patronyme->origine)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Origine</h4>
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $patronyme->origine }}</p>
                                    </div>
                                @endif

                                @if($patronyme->full_location)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-1">Localisation</h4>
                                        <p class="text-sm text-gray-600">{{ $patronyme->full_location }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('patronymes.show', $patronyme) }}"
                                   class="btn btn-primary text-sm px-4 py-2 w-full text-center">
                                    Voir plus
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $patronymes->links() }}
            </div>
        @else
            <!-- No Results -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun résultat trouvé</h3>
                <p class="text-gray-600 mb-6">
                    Essayez de modifier vos critères de recherche ou explorez nos patronymes populaires.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('patronymes.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh mr-2"></i>Voir tous les patronymes
                    </a>
                    <a href="{{ route('patronymes.index') }}?featured=1" class="btn btn-outline">
                        <i class="fas fa-star mr-2"></i>Patronymes populaires
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Optimized JavaScript for fluid search -->
    <script>
        console.log('Search script loading...');

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing search...');

            const searchInput = document.getElementById('search-input');
            const suggestionsDropdown = document.getElementById('suggestions-dropdown');
            const loadingIndicator = document.getElementById('loading-indicator');
            let debounceTimer = null;
            let realTimeDebounceTimer = null;

            if (!searchInput) {
                console.error('Search input not found!');
                return;
            }

            console.log('Search elements found:', {
                searchInput: !!searchInput,
                suggestionsDropdown: !!suggestionsDropdown,
                loadingIndicator: !!loadingIndicator
            });

            // Show/hide suggestions
            function showSuggestions() {
                if (suggestionsDropdown) {
                    suggestionsDropdown.classList.remove('hidden');
                    console.log('Showing suggestions');
                }
            }

            function hideSuggestions() {
                if (suggestionsDropdown) {
                    suggestionsDropdown.classList.add('hidden');
                    console.log('Hiding suggestions');
                }
            }

            // Show/hide loading indicator
            function showLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.remove('hidden');
                    console.log('Showing loading');
                }
            }

            function hideLoading() {
                if (loadingIndicator) {
                    loadingIndicator.classList.add('hidden');
                    console.log('Hiding loading');
                }
            }

            // Get suggestions from server
            function getSuggestions(query) {
                console.log('Getting suggestions for:', query);

                if (query.length < 2) {
                    hideSuggestions();
                    return;
                }

                showLoading();

                // Debounce the request
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    console.log('Making request to:', '{{ route('patronymes.suggestions') }}?q=' + encodeURIComponent(query));

                    fetch('/search/suggestions?q=' + encodeURIComponent(query) + '&limit=8')
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Suggestions received:', data);
                            displaySuggestions(data);
                            hideLoading();
                        })
                        .catch(error => {
                            console.error('Error fetching suggestions:', error);
                            hideSuggestions();
                            hideLoading();
                        });
                }, 300);
            }

            // Real-time search results
            function performRealTimeSearch(query) {
                console.log('Performing real-time search for:', query);

                if (query.length < 2) {
                    hideRealTimeResults();
                    return;
                }

                showRealTimeLoading();

                // Debounce the request
                clearTimeout(realTimeDebounceTimer);
                realTimeDebounceTimer = setTimeout(() => {
                    // Normalize search query for better matching
                    const normalizedQuery = normalizeSearchQuery(query);

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
                            displayRealTimeResults(data);
                            hideRealTimeLoading();
                        })
                        .catch(error => {
                            console.error('Error fetching real-time results:', error);
                            hideRealTimeResults();
                            hideRealTimeLoading();
                        });
                }, 500);
            }

            // Normalize search query for better matching
            function normalizeSearchQuery(query) {
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
            }

            // Display suggestions in dropdown
            function displaySuggestions(suggestions) {
                console.log('Displaying suggestions:', suggestions);

                if (!suggestions || suggestions.length === 0) {
                    hideSuggestions();
                    return;
                }

                const html = suggestions.map(suggestion => `
                    <div class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 suggestion-item"
                         data-value="${suggestion.value}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">${suggestion.label}</div>
                                ${suggestion.description ? `<div class="text-sm text-gray-500">${suggestion.description}</div>` : ''}
                            </div>
                            <div class="text-xs text-blue-500 font-medium">${suggestion.type}</div>
                        </div>
                    </div>
                `).join('');

                if (suggestionsDropdown) {
                    suggestionsDropdown.innerHTML = html;
                    showSuggestions();

                    // Add click listeners to suggestions
                    suggestionsDropdown.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const value = this.getAttribute('data-value');
                            console.log('Suggestion clicked:', value);
                            searchInput.value = value;
                            hideSuggestions();

                            // Auto-submit the form
                            const form = document.querySelector('form');
                            if (form) {
                                console.log('Submitting form...');
                                form.submit();
                            }
                        });
                    });
                }
            }

            // Real-time search functions
            function showRealTimeLoading() {
                const realTimeContainer = document.getElementById('real-time-results');
                if (realTimeContainer) {
                    realTimeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche en cours...</div>';
                    realTimeContainer.classList.remove('hidden');
                }
            }

            function hideRealTimeLoading() {
                // Loading will be replaced by results
            }

            function hideRealTimeResults() {
                const realTimeContainer = document.getElementById('real-time-results');
                if (realTimeContainer) {
                    realTimeContainer.classList.add('hidden');
                }
            }

            function displayRealTimeResults(data) {
                const realTimeContainer = document.getElementById('real-time-results');
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
                            <button onclick="hideRealTimeResults()"
                                    class="text-gray-500 hover:text-gray-700 text-sm">
                                <i class="fas fa-times mr-1"></i>Masquer les résultats en temps réel
                            </button>
                        </div>
                    </div>
                `;

                realTimeContainer.innerHTML = html;
                realTimeContainer.classList.remove('hidden');
            }

            // Event listeners
            searchInput.addEventListener('input', function() {
                console.log('Input event:', this.value);
                const query = this.value.trim();

                // Show suggestions for autocomplete
                getSuggestions(query);

                // Show real-time results
                performRealTimeSearch(query);
            });

            searchInput.addEventListener('focus', function() {
                console.log('Focus event');
                if (suggestionsDropdown && suggestionsDropdown.innerHTML.trim() !== '') {
                    showSuggestions();
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#search-container')) {
                    hideSuggestions();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            console.log('Search functionality initialized successfully');
        });

        // Toggle favorite function
        function toggleFavorite(patronymeId) {
            console.log('Toggling favorite for patronyme:', patronymeId);

            fetch(`/patronymes/${patronymeId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Favorite response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Favorite response data:', data);

                // Update heart icon
                const heartIcon = document.querySelector(`button[data-patronyme-id="${patronymeId}"] i`);
                if (heartIcon) {
                    if (data.isFavorited) {
                        heartIcon.classList.add('text-red-500');
                        heartIcon.classList.remove('far');
                        heartIcon.classList.add('fas');
                    } else {
                        heartIcon.classList.remove('text-red-500');
                        heartIcon.classList.remove('fas');
                        heartIcon.classList.add('far');
                    }
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
                alert('Erreur lors de l\'ajout aux favoris. Veuillez réessayer.');
            });
        }
    </script>
</x-app-layout>
