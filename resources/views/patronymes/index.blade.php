<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-users"></i>{{ __('Répertoire des Patronymes') }}
            </h2>
            @auth
                @if(Auth::user()->isAdmin() || Auth::user()->isContributeur())
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
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recherche</label>
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="Nom du patronyme..."
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
        // Mise à jour dynamique des départements selon la région sélectionnée
        document.querySelector('select[name="region_id"]')?.addEventListener('change', function() {
            const regionId = this.value;
            const departementSelect = document.querySelector('select[name="departement_id"]');

            if (regionId && departementSelect) {
                fetch(`/api/departements?region_id=${regionId}`)
                    .then(response => response.json())
                    .then(data => {
                        departementSelect.innerHTML = '<option value="">Tous les départements</option>';
                        data.forEach(departement => {
                            departementSelect.innerHTML += `<option value="${departement.id}">${departement.name}</option>`;
                        });
                    });
            }
        });
    </script>
    @endpush
</x-app-layout>
