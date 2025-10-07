<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-search-plus text-blue-600 mr-2"></i>
                    Recherche Avancée
                </h1>
                <p class="text-gray-600 mt-1">
                    Recherchez des patronymes avec des critères précis
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('patronymes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à la recherche simple
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Advanced Search Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form id="advanced-search-form" class="space-y-6">
                <!-- Search Term -->
                <div>
                    <label class="form-label">Recherche</label>
                    <div class="relative" id="search-container">
                        <input
                            type="text"
                            name="search"
                            id="advanced-search-input"
                            placeholder="Tapez un nom de patronyme... (ex: 'Traoré', 'Ouédraogo')"
                            class="form-input pl-10"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <div id="advanced-loading-indicator" class="absolute inset-y-0 right-0 pr-3 items-center hidden">
                            <i class="fas fa-spinner fa-spin text-blue-500"></i>
                        </div>
                        <div id="advanced-suggestions-dropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                        </div>
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Region Filter -->
                    <div>
                        <label for="region_id" class="form-label">Région</label>
                        <select name="region_id" id="region_id" class="form-select">
                            <option value="">Toutes les régions</option>
                        </select>
                    </div>

                    <!-- Ethnic Group Filter -->
                    <div>
                        <label for="groupe_ethnique_id" class="form-label">Groupe ethnique</label>
                        <select name="groupe_ethnique_id" id="groupe_ethnique_id" class="form-select">
                            <option value="">Tous les groupes</option>
                        </select>
                    </div>

                    <!-- Language Filter -->
                    <div>
                        <label for="langue_id" class="form-label">Langue</label>
                        <select name="langue_id" id="langue_id" class="form-select">
                            <option value="">Toutes les langues</option>
                        </select>
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sort_by" class="form-label">Trier par</label>
                        <select name="sort_by" id="sort_by" class="form-select">
                            <option value="relevance">Pertinence</option>
                            <option value="name">Nom (A-Z)</option>
                            <option value="popularity">Popularité</option>
                            <option value="recent">Plus récents</option>
                            <option value="featured">Mis en avant</option>
                        </select>
                    </div>
                    <div>
                        <label for="per_page" class="form-label">Résultats par page</label>
                        <select name="per_page" id="per_page" class="form-select">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    <button type="button" id="clear-filters" class="btn btn-secondary flex-1 sm:flex-none">
                        <i class="fas fa-times mr-2"></i>Effacer les filtres
                    </button>
                    <button type="button" id="show-simple-search" class="btn btn-outline flex-1 sm:flex-none">
                        <i class="fas fa-search-minus mr-2"></i>Recherche simple
                    </button>
                </div>
            </form>
        </div>

        <!-- Real-time Search Results -->
        <div id="advanced-real-time-results" class="hidden">
            <!-- Real-time search results will be displayed here -->
        </div>

        <!-- Search Statistics -->
        <div id="search-stats" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 hidden">
            <div class="card">
                <div class="p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600" id="total-patronymes">-</div>
                    <div class="text-sm text-gray-600">Patronymes</div>
                </div>
            </div>
            <div class="card">
                <div class="p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" id="total-regions">-</div>
                    <div class="text-sm text-gray-600">Régions</div>
                </div>
            </div>
            <div class="card">
                <div class="p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600" id="total-ethnies">-</div>
                    <div class="text-sm text-gray-600">Groupes ethniques</div>
                </div>
            </div>
            <div class="card">
                <div class="p-4 text-center">
                    <div class="text-2xl font-bold text-orange-600" id="total-langues">-</div>
                    <div class="text-sm text-gray-600">Langues</div>
                </div>
            </div>
        </div>

        <!-- Popular Searches -->
        <div id="popular-searches" class="bg-white rounded-xl shadow-lg p-6 mb-8 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-fire text-red-500 mr-2"></i>
                Recherches populaires
            </h3>
            <div id="popular-searches-list" class="flex flex-wrap gap-2">
                <!-- Popular searches will be loaded here -->
            </div>
        </div>

        <!-- Search Results -->
        <div id="search-results" class="hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">
                    <span id="results-count">0</span> résultat(s) trouvé(s)
                </h2>
                <div class="text-sm text-gray-500">
                    <span id="search-time">-</span>ms
                </div>
            </div>

            <!-- Results Grid -->
            <div id="results-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Results will be loaded here -->
            </div>

            <!-- Pagination -->
            <div id="results-pagination" class="flex justify-center">
                <!-- Pagination will be loaded here -->
            </div>
        </div>

        <!-- No Results -->
        <div id="no-results" class="text-center py-12 hidden">
            <div class="max-w-md mx-auto">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun résultat trouvé</h3>
                <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche ou d'utiliser des termes plus généraux.</p>
                <button id="suggest-alternatives" class="btn btn-primary">
                    <i class="fas fa-lightbulb mr-2"></i>Voir des suggestions
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('advanced-search-input');
        const suggestionsDropdown = document.getElementById('advanced-suggestions-dropdown');
        const loadingIndicator = document.getElementById('advanced-loading-indicator');
        const searchForm = document.getElementById('advanced-search-form');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const showSimpleSearchBtn = document.getElementById('show-simple-search');

        let searchTimeout;

        // Load search filters
        loadSearchFilters();
        loadSearchStats();
        loadPopularSearches();

        // Search input with suggestions
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length < 2) {
                suggestionsDropdown.classList.add('hidden');
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadSuggestions(query);
            }, 300);
        });

        // Handle suggestion selection
        suggestionsDropdown.addEventListener('click', function (e) {
            if (e.target.classList.contains('suggestion-item')) {
                searchInput.value = e.target.dataset.value;
                suggestionsDropdown.classList.add('hidden');
                performSearch();
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#search-container')) {
                suggestionsDropdown.classList.add('hidden');
            }
        });

        // Form submission
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            performSearch();
        });

        // Clear filters
        clearFiltersBtn.addEventListener('click', function () {
            searchForm.reset();
            hideResults();
        });

        // Show simple search
        showSimpleSearchBtn.addEventListener('click', function () {
            window.location.href = '{{ route("patronymes.index") }}';
        });

        function loadSuggestions(query) {
            loadingIndicator.classList.remove('hidden');

            fetch(`/search/suggestions?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displaySuggestions(data);
                })
                .catch(error => {
                    console.error('Error loading suggestions:', error);
                })
                .finally(() => {
                    loadingIndicator.classList.add('hidden');
                });
        }

        function displaySuggestions(suggestions) {
            if (suggestions.length === 0) {
                suggestionsDropdown.classList.add('hidden');
                return;
            }

            const html = suggestions.map(item => `
                <div class="suggestion-item px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                     data-value="${item.value}">
                    <div class="font-medium text-gray-900">${item.label}</div>
                    ${item.description ? `<div class="text-sm text-gray-500 mt-1">${item.description}</div>` : ''}
                </div>
            `).join('');

            suggestionsDropdown.innerHTML = html;
            suggestionsDropdown.classList.remove('hidden');
        }

        function performSearch() {
            const formData = new FormData(searchForm);
            const criteria = Object.fromEntries(formData.entries());

            // Remove empty values
            Object.keys(criteria).forEach(key => {
                if (!criteria[key]) {
                    delete criteria[key];
                }
            });

            const startTime = performance.now();
            loadingIndicator.classList.remove('hidden');

            fetch('/search/advanced', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(criteria)
            })
            .then(response => response.json())
            .then(data => {
                displayResults(data);
                const endTime = performance.now();
                document.getElementById('search-time').textContent = Math.round(endTime - startTime);
            })
            .catch(error => {
                console.error('Error performing search:', error);
                showError('Une erreur est survenue lors de la recherche.');
            })
            .finally(() => {
                loadingIndicator.classList.add('hidden');
            });
        }

        function displayResults(data) {
            const resultsGrid = document.getElementById('results-grid');
            const resultsCount = document.getElementById('results-count');
            const searchResults = document.getElementById('search-results');
            const noResults = document.getElementById('no-results');

            if (data.data.length === 0) {
                searchResults.classList.add('hidden');
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');
            searchResults.classList.remove('hidden');

            resultsCount.textContent = data.pagination.total;

            const html = data.data.map(patronyme => `
                <div class="card card-hover">
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">
                                        ${patronyme.nom}
                                    </h3>
                                    ${patronyme.groupe_ethnique ? `<p class="text-sm text-blue-600 mb-2">${patronyme.groupe_ethnique.nom}</p>` : ''}
                                </div>
                                ${patronyme.is_featured ? '<span class="badge badge-primary">Populaire</span>' : ''}
                            </div>
                        </div>

                        ${patronyme.signification ? `
                            <div class="mb-4">
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    ${patronyme.signification}
                                </p>
                            </div>
                        ` : ''}

                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center space-x-4">
                                ${patronyme.region ? `<span><i class="fas fa-map-marker-alt mr-1"></i>${patronyme.region.nom}</span>` : ''}
                                ${patronyme.langue ? `<span><i class="fas fa-language mr-1"></i>${patronyme.langue.nom}</span>` : ''}
                            </div>
                            ${patronyme.views_count > 0 ? `<span><i class="fas fa-eye mr-1"></i>${patronyme.views_count}</span>` : ''}
                        </div>
                    </div>
                </div>
            `).join('');

            resultsGrid.innerHTML = html;
        }

        function loadSearchFilters() {
            fetch('/search/filters')
                .then(response => response.json())
                .then(data => {
                    populateSelect('region_id', data.regions);
                    populateSelect('groupe_ethnique_id', data.groupe_ethniques);
                    populateSelect('langue_id', data.langues);
                })
                .catch(error => {
                    console.error('Error loading filters:', error);
                });
        }

        function loadSearchStats() {
            fetch('/search/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-patronymes').textContent = data.total_patronymes.toLocaleString();
                    document.getElementById('total-regions').textContent = data.total_regions;
                    document.getElementById('total-ethnies').textContent = data.total_ethnies;
                    document.getElementById('total-langues').textContent = data.total_langues;
                    document.getElementById('search-stats').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        function loadPopularSearches() {
            fetch('/search/popular?limit=10')
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const html = data.map(item => `
                            <button class="badge badge-outline hover:badge-primary cursor-pointer"
                                    onclick="searchPopular('${item.term}')">
                                ${item.term}
                            </button>
                        `).join('');

                        document.getElementById('popular-searches-list').innerHTML = html;
                        document.getElementById('popular-searches').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading popular searches:', error);
                });
        }

        function populateSelect(selectId, data) {
            const select = document.getElementById(selectId);
            const currentValue = select.value;

            select.innerHTML = select.querySelector('option[value=""]').outerHTML;

            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nom;
                select.appendChild(option);
            });

            select.value = currentValue;
        }

        function hideResults() {
            document.getElementById('search-results').classList.add('hidden');
            document.getElementById('no-results').classList.add('hidden');
        }

        function showError(message) {
            // You can implement a toast notification here
            alert(message);
        }

        // Global function for popular search
        window.searchPopular = function(term) {
            searchInput.value = term;
            performSearch();
        };

        // Real-time search functionality
        let realTimeDebounceTimer = null;

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

        // Real-time search functions
        function showRealTimeLoading() {
            const realTimeContainer = document.getElementById('advanced-real-time-results');
            if (realTimeContainer) {
                realTimeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche en cours...</div>';
                realTimeContainer.classList.remove('hidden');
            }
        }

        function hideRealTimeLoading() {
            // Loading will be replaced by results
        }

        function hideRealTimeResults() {
            const realTimeContainer = document.getElementById('advanced-real-time-results');
            if (realTimeContainer) {
                realTimeContainer.classList.add('hidden');
            }
        }

        function displayRealTimeResults(data) {
            const realTimeContainer = document.getElementById('advanced-real-time-results');
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

        // Add event listener for real-time search
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Show suggestions for autocomplete
            getSuggestions(query);

            // Show real-time results
            performRealTimeSearch(query);
        });

        // Global function to hide real-time results
        window.hideRealTimeResults = hideRealTimeResults;
    });
    </script>
</x-app-layout>
