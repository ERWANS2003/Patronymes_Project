<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gradient-bf">
                <i class="mr-2 fas fa-plus-circle"></i>{{ __('FICHE DE COLLECTE DES PATRONYMES NATIONAUX') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-bfNavy-600">Étape 3 sur 3</span>
                <div class="w-32 bg-bfNavy-200 rounded-full h-2">
                    <div class="bg-gradient-bf-flag h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-glow-bf sm:rounded-lg">
                <div class="p-6">
                    <!-- Progress indicator -->
                    <div class="mb-8">
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-bfGreen-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <span class="ml-2 text-sm font-medium text-bfGreen-600">Identité</span>
                            </div>
                            <div class="w-16 h-0.5 bg-bfGreen-600"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-bfGreen-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <span class="ml-2 text-sm font-medium text-bfGreen-600">Patronyme</span>
                            </div>
                            <div class="w-16 h-0.5 bg-bfGreen-600"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-bfGreen-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                <span class="ml-2 text-sm font-medium text-bfGreen-600">Localisation</span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-bfRed-50 border-l-4 border-bfRed-500 text-bfRed-700 rounded shadow-sm">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-circle mr-2 text-bfRed-500"></i>
                                <span class="font-bold">Des erreurs ont été détectées :</span>
                            </div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('patronymes.store') }}" method="POST">
                        @csrf

                        <!-- Section III: LOCALISATION -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gradient-bf mb-6 bg-gradient-to-r from-bfRed-50 to-bfGold-50 p-4 rounded-lg border border-bfRed-200">
                                <i class="fas fa-map mr-2 text-bfRed-600"></i>
                                III. LOCALISATION
                            </h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="region_id" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-map-marked-alt mr-1 text-bfRed-600"></i>Région
                                        </label>
                                        <select name="region_id" id="region_id"
                                            class="form-select focus:ring-bfRed-500 focus:border-bfRed-500">
                                            <option value="">Sélectionnez une région</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}" {{ old('region_id', $patronymeData['region_id'] ?? '') == $region->id ? 'selected' : '' }}>
                                                    {{ $region->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="province_id" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-building mr-1 text-bfRed-600"></i>Province
                                        </label>
                                        <select name="province_id" id="province_id"
                                            class="form-select focus:ring-bfRed-500 focus:border-bfRed-500">
                                            <option value="">Sélectionnez une province</option>
                                        </select>
                                        @error('province_id')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="commune_id" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-home mr-1 text-bfRed-600"></i>Commune
                                    </label>
                                    <select name="commune_id" id="commune_id"
                                        class="form-select focus:ring-bfRed-500 focus:border-bfRed-500">
                                        <option value="">Sélectionnez une commune</option>
                                    </select>
                                    @error('commune_id')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Récapitulatif des données -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gradient-bf mb-6 bg-gradient-to-r from-bfGreen-50 to-bfGold-50 p-4 rounded-lg border border-bfGreen-200">
                                <i class="fas fa-clipboard-check mr-2 text-bfGreen-600"></i>
                                RÉCAPITULATIF
                            </h3>

                            <div class="bg-bfGreen-50 border border-bfGreen-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <h4 class="font-semibold text-bfGreen-800 mb-2">Informations de l'enquêté :</h4>
                                        <p class="@error('enquete_nom') text-bfRed-600 font-bold @enderror">
                                            <strong>Nom :</strong> {{ $patronymeData['enquete_nom'] ?? 'Non renseigné' }}
                                        </p>
                                        <p><strong>Âge :</strong> {{ $patronymeData['enquete_age'] ?? 'Non renseigné' }}</p>
                                        <p><strong>Sexe :</strong> {{ $patronymeData['enquete_sexe'] ?? 'Non renseigné' }}</p>
                                        <p><strong>Fonction :</strong> {{ $patronymeData['enquete_fonction'] ?? 'Non renseigné' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-bfGreen-800 mb-2">Informations du patronyme :</h4>
                                        <p class="@error('nom') text-bfRed-600 font-bold @enderror">
                                            <strong>Patronyme :</strong> {{ $patronymeData['nom'] ?? 'Non renseigné' }}
                                        </p>
                                        <p><strong>Groupe ethnique :</strong> {{ $patronymeData['groupe_ethnique_nom'] ?? 'Non renseigné' }}</p>
                                        <p><strong>Langue :</strong> {{ $patronymeData['langue_nom'] ?? 'Non renseigné' }}</p>
                                        <p><strong>Transmission :</strong> {{ $patronymeData['transmission'] ?? 'Non renseigné' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('patronymes.create.step2') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i> Précédent
                            </a>
                            <div class="flex space-x-3">
                                <a href="{{ route('patronymes.index') }}" class="btn btn-secondary">
                                    <i class="mr-2 fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary shadow-glow-bf">
                                    <i class="mr-2 fas fa-save"></i> Enregistrer le patronyme
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const regionSelect = document.querySelector('select[name="region_id"]');
        const provinceSelect = document.querySelector('select[name="province_id"]');
        const communeSelect = document.querySelector('select[name="commune_id"]');

        regionSelect?.addEventListener('change', function () {
            const regionId = this.value;
            provinceSelect && (provinceSelect.innerHTML = '<option value="">Sélectionnez une province</option>');
            communeSelect && (communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>');
            if (!regionId) return;

            console.log('Chargement des provinces pour la région:', regionId);
            fetch(`{{ url('/get-provinces') }}?region_id=${regionId}`)
                .then(res => {
                    console.log('Réponse provinces:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Données provinces reçues:', data);
                    data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.id}">${province.nom}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des provinces:', error);
                });
        });

        provinceSelect?.addEventListener('change', function () {
            const provinceId = this.value;
            communeSelect && (communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>');
            if (!provinceId) return;

            console.log('Chargement des communes pour la province:', provinceId);
            fetch(`{{ url('/get-communes') }}?province_id=${provinceId}`)
                .then(res => {
                    console.log('Réponse communes:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Données communes reçues:', data);
                    data.forEach(commune => {
                        communeSelect.innerHTML += `<option value="${commune.id}">${commune.nom}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des communes:', error);
                });
        });
    });
    </script>
</x-app-layout>
