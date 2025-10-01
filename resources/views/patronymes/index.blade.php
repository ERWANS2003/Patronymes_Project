<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Répertoire des Patronymes
                </h1>
                <p class="text-gray-600 mt-1">
                    Découvrez l'origine et la signification des noms de famille du Burkina Faso
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">
                    {{ $patronymes->total() }} patronyme{{ $patronymes->total() > 1 ? 's' : '' }} trouvé{{ $patronymes->total() > 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Advanced Search Component -->
        <x-advanced-search />

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('patronymes.index') }}" class="space-y-6">
                <!-- Search Input with Autocomplete -->
                <div>
                    <label class="form-label">Recherche</label>
                    <div class="relative" x-data="searchAutocomplete()">
                        <input
                            type="text"
                            name="search"
                            placeholder="Tapez un nom de patronyme... (ex: 'e' pour voir tous les patronymes commençant par 'e')"
                            class="form-input pl-10"
                            value="{{ request('search') }}"
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

                <!-- Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Region Filter -->
                    <div>
                        <label class="form-label">Région</label>
                        <select name="region_id" class="form-select" id="region-select">
                            <option value="">Toutes les régions</option>
                            @foreach(\App\Models\Region::all() as $region)
                                <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Province Filter -->
                    <div>
                        <label class="form-label">Province</label>
                        <select name="province_id" class="form-select" id="province-select" {{ empty($provinceId) ? 'disabled' : '' }}>
                            <option value="">Toutes les provinces</option>
                            @if($provinceId)
                                @foreach(\App\Models\Province::where('region_id', $provinceId)->get() as $province)
                                    <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>
                                        {{ $province->nom }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Commune Filter -->
                    <div>
                        <label class="form-label">Commune</label>
                        <select name="commune_id" class="form-select" id="commune-select" {{ empty($provinceId) ? 'disabled' : '' }}>
                            <option value="">Toutes les communes</option>
                            @if($provinceId)
                                @foreach(\App\Models\Commune::where('province_id', $provinceId)->get() as $commune)
                                    <option value="{{ $commune->id }}" {{ request('commune_id') == $commune->id ? 'selected' : '' }}>
                                        {{ $commune->nom }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Groupe Ethnique Filter -->
                    <div>
                        <label class="form-label">Groupe ethnique</label>
                        <select name="groupe_ethnique_id" class="form-select">
                            <option value="">Tous les groupes</option>
                            @foreach(\App\Models\GroupeEthnique::all() as $groupe)
                                <option value="{{ $groupe->id }}" {{ request('groupe_ethnique_id') == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        <i class="fas fa-search mr-2"></i>Rechercher
                    </button>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-secondary flex-1 sm:flex-none">
                        <i class="fas fa-times mr-2"></i>Effacer
                    </a>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if($patronymes->count() > 0)
            <!-- Sort Options -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                    <span class="text-sm text-gray-600">Trier par:</span>
                    <div class="flex space-x-2">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nom']) }}"
                           class="px-3 py-1 text-sm rounded-lg {{ request('sort') == 'nom' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Nom
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at']) }}"
                           class="px-3 py-1 text-sm rounded-lg {{ request('sort') == 'created_at' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Plus récent
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'views_count']) }}"
                           class="px-3 py-1 text-sm rounded-lg {{ request('sort') == 'views_count' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Plus populaire
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Vue:</span>
                    <button class="p-2 rounded-lg bg-blue-100 text-blue-700">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="p-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Patronymes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($patronymes as $patronyme)
                    <div class="card card-hover">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">
                                        {{ $patronyme->nom }}
                                    </h3>
                                    @if($patronyme->groupeEthnique)
                                        <span class="badge badge-primary">
                                            {{ $patronyme->groupeEthnique->nom }}
                                        </span>
                                    @endif
                                </div>
                                @auth
                                    <button class="text-gray-400 hover:text-red-500 transition-colors"
                                            onclick="toggleFavorite({{ $patronyme->id }})">
                                        <i class="fas fa-heart {{ $patronyme->isFavoritedBy(Auth::id()) ? 'text-red-500' : '' }}"></i>
                                    </button>
                                @endauth
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

                            <!-- Stats -->
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ $patronyme->views_count }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-heart mr-1"></i>
                                        {{ $patronyme->favorites()->count() }}
                                    </span>
                                </div>
                                <a href="{{ route('patronymes.show', $patronyme) }}"
                                   class="btn btn-primary text-sm px-4 py-2">
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

    <!-- JavaScript for dynamic filters -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const regionSelect = document.getElementById('region-select');
            const provinceSelect = document.getElementById('province-select');
            const communeSelect = document.getElementById('commune-select');

            // Region change handler
            regionSelect.addEventListener('change', function() {
                const regionId = this.value;

                // Reset province and commune
                provinceSelect.innerHTML = '<option value="">Toutes les provinces</option>';
                communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
                provinceSelect.disabled = !regionId;
                communeSelect.disabled = true;

                if (regionId) {
                    // Fetch provinces for selected region
                    fetch(`/api/regions/${regionId}/provinces`)
                        .then(response => response.json())
                        .then(provinces => {
                            provinces.forEach(province => {
                                const option = document.createElement('option');
                                option.value = province.id;
                                option.textContent = province.nom;
                                provinceSelect.appendChild(option);
                            });
                            provinceSelect.disabled = false;
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            // Province change handler
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;

                // Reset commune
                communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
                communeSelect.disabled = !provinceId;

                if (provinceId) {
                    // Fetch communes for selected province
                    fetch(`/api/provinces/${provinceId}/communes`)
                        .then(response => response.json())
                        .then(communes => {
                            communes.forEach(commune => {
                                const option = document.createElement('option');
                                option.value = commune.id;
                                option.textContent = commune.nom;
                                communeSelect.appendChild(option);
                            });
                            communeSelect.disabled = false;
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });

        // Toggle favorite function
        function toggleFavorite(patronymeId) {
            fetch(`/patronymes/${patronymeId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update heart icon
                    const heartIcon = event.target;
                    if (data.favorited) {
                        heartIcon.classList.add('text-red-500');
                    } else {
                        heartIcon.classList.remove('text-red-500');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Search Autocomplete Function
        function searchAutocomplete() {
            return {
                searchQuery: '{{ request('search') }}',
                suggestions: [],
                showSuggestions: false,
                debounceTimer: null,

                getSuggestions(query) {
                    if (query.length < 2) {
                        this.suggestions = [];
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
                },

                selectSuggestion(suggestion) {
                    this.searchQuery = suggestion.value;
                    this.showSuggestions = false;

                    // Update the form input
                    const searchInput = document.querySelector('input[name="search"]');
                    if (searchInput) {
                        searchInput.value = suggestion.value;
                    }
                }
            }
        }
    </script>
</x-app-layout>
