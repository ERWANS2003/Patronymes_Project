<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-users"></i>{{ __('Répertoire des Patronymes') }}
            </h2>
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('patronymes.create') }}" class="flex items-center px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        <i class="mr-2 fas fa-plus"></i> Ajouter un patronyme
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Filtres de recherche -->
            <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('patronymes.index') }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recherche avancée</label>
                                <div class="relative">
                                    <input type="text" name="search" id="search-input" value="{{ $search ?? '' }}"
                                        placeholder="Nom, signification, origine, région, groupe ethnique..."
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        autocomplete="off">
                                    <div id="search-suggestions" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                                        <!-- Les suggestions apparaîtront ici -->
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Recherche dans le nom, signification, origine, histoire, région, province, commune, groupe ethnique, langue...
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Région</label>
                                <select name="region_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les régions</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ ($regionId ?? '') == $region->id ? 'selected' : '' }}>
                                            {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Province</label>
                                <select name="province_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les provinces</option>
                                    @foreach(($provinces ?? []) as $province)
                                        <option value="{{ $province->id }}" {{ ($provinceId ?? '') == $province->id ? 'selected' : '' }}>
                                            {{ $province->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Commune</label>
                                <select name="commune_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les communes</option>
                                    @foreach(($communes ?? []) as $commune)
                                        <option value="{{ $commune->id }}" {{ ($communeId ?? '') == $commune->id ? 'selected' : '' }}>
                                            {{ $commune->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Groupe ethnique</label>
                                <select name="groupe_ethnique_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les groupes</option>
                                    @foreach($groupesEthniques as $groupe)
                                        <option value="{{ $groupe->id }}" {{ ($groupeEthniqueId ?? '') == $groupe->id ? 'selected' : '' }}>
                                            {{ $groupe->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ethnie</label>
                                <select name="ethnie_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les ethnies</option>
                                    @foreach(($ethnies ?? []) as $ethnie)
                                        <option value="{{ $ethnie->id }}" {{ ($ethnieId ?? '') == $ethnie->id ? 'selected' : '' }}>
                                            {{ $ethnie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Langue</label>
                                <select name="langue_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les langues</option>
                                    @foreach(($langues ?? []) as $langue)
                                        <option value="{{ $langue->id }}" {{ ($langueId ?? '') == $langue->id ? 'selected' : '' }}>
                                            {{ $langue->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <i class="mr-2 fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des patronymes -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($patronymes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Nom
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Origine
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Région
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Groupe ethnique
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($patronymes as $patronyme)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('patronymes.show', $patronyme) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $patronyme->nom }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->origine ?? 'Non spécifiée' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->region->name ?? 'Non spécifiée' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->groupeEthnique->nom ?? 'Non spécifié' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <a href="{{ route('patronymes.show', $patronyme) }}" class="mr-3 text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                @auth
                                                    @if(Auth::user()->isAdmin())
                                                        <a href="{{ route('patronymes.edit', $patronyme) }}" class="mr-3 text-yellow-600 hover:text-yellow-900">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('patronymes.destroy', $patronyme) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patronyme ?')">
                                                                <i class="fas fa-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $patronymes->links() }}
                        </div>
                    @else
                        <div class="py-12 text-center">
                            <i class="mb-4 text-5xl text-gray-400 fas fa-search"></i>
                            <p class="text-lg text-gray-500">Aucun patronyme trouvé.</p>
                            @if(request()->anyFilled(['search', 'region_id', 'groupe_ethnique_id']))
                                <a href="{{ route('patronymes.index') }}" class="inline-block mt-2 text-indigo-600 hover:text-indigo-800">
                                    Réinitialiser les filtres
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Mise à jour dynamique des provinces et communes selon la région / province sélectionnée
        function loadProvinces(regionId, selectedProvinceId) {
            const provinceSelect = document.querySelector('select[name="province_id"]');
            const communeSelect = document.querySelector('select[name="commune_id"]');
            if (!provinceSelect) return;
            provinceSelect.innerHTML = '<option value="">Toutes les provinces</option>';
            communeSelect && (communeSelect.innerHTML = '<option value="">Toutes les communes</option>');
            if (!regionId) return;
            fetch(`/get-provinces?region_id=${regionId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(province => {
                        const selected = selectedProvinceId && String(selectedProvinceId) === String(province.id) ? 'selected' : '';
                        provinceSelect.innerHTML += `<option value="${province.id}" ${selected}>${province.nom}</option>`;
                    });
                });
        }

        function loadCommunes(provinceId, selectedCommuneId) {
            const communeSelect = document.querySelector('select[name="commune_id"]');
            if (!communeSelect) return;
            communeSelect.innerHTML = '<option value="">Toutes les communes</option>';
            if (!provinceId) return;
            fetch(`/get-communes?province_id=${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(commune => {
                        const selected = selectedCommuneId && String(selectedCommuneId) === String(commune.id) ? 'selected' : '';
                        communeSelect.innerHTML += `<option value="${commune.id}" ${selected}>${commune.nom}</option>`;
                    });
                });
        }

        document.querySelector('select[name="region_id"]')?.addEventListener('change', function() {
            const regionId = this.value;
            loadProvinces(regionId, null);
        });

        document.querySelector('select[name="province_id"]')?.addEventListener('change', function() {
            const provinceId = this.value;
            loadCommunes(provinceId, null);
        });

        // Prefill on page load when region/province already selected
        document.addEventListener('DOMContentLoaded', function () {
            const regionSelect = document.querySelector('select[name="region_id"]');
            const provinceSelect = document.querySelector('select[name="province_id"]');
            const communeSelect = document.querySelector('select[name="commune_id"]');
            const selectedRegionId = regionSelect ? regionSelect.value : '';
            const selectedProvinceId = provinceSelect ? provinceSelect.getAttribute('data-selected') || provinceSelect.value : '';
            const selectedCommuneId = communeSelect ? communeSelect.getAttribute('data-selected') || communeSelect.value : '';

            if (selectedRegionId) {
                loadProvinces(selectedRegionId, selectedProvinceId);
                if (selectedProvinceId) {
                    // Delay to ensure provinces load first
                    setTimeout(() => loadCommunes(selectedProvinceId, selectedCommuneId), 200);
                }
            }
        });

        // Autocomplétion pour la recherche
        const searchInput = document.getElementById('search-input');
        const suggestionsDiv = document.getElementById('search-suggestions');
        let searchTimeout;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(suggestions => {
                        if (suggestions.length > 0) {
                            displaySuggestions(suggestions);
                        } else {
                            suggestionsDiv.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        suggestionsDiv.classList.add('hidden');
                    });
            }, 300);
        });

        function displaySuggestions(suggestions) {
            suggestionsDiv.innerHTML = '';

            suggestions.forEach(suggestion => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="text-sm">${suggestion.label}</span>
                        <span class="text-xs text-gray-500">${suggestion.type}</span>
                    </div>
                `;

                div.addEventListener('click', function() {
                    searchInput.value = suggestion.value;
                    suggestionsDiv.classList.add('hidden');
                    searchInput.form.submit();
                });

                suggestionsDiv.appendChild(div);
            });

            suggestionsDiv.classList.remove('hidden');
        }

        // Masquer les suggestions quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.classList.add('hidden');
            }
        });

        // Masquer les suggestions avec Escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                suggestionsDiv.classList.add('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>
