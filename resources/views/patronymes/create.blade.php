<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-plus-circle"></i>{{ __('FICHE DE COLLECTE DES PATRONYMES NATIONAUX') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('patronymes.store') }}" method="POST">
                        @csrf

                        <!-- Informations sur l'enquêté -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 bg-gray-100 p-3 rounded">
                                I. IDENTITÉ DE L'ENQUÊTÉ
                            </h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="enquete_nom" class="block text-sm font-medium text-gray-700">NOM ET PRÉNOM:</label>
                                    <input type="text" name="enquete_nom" id="enquete_nom" required
                                        value="{{ old('enquete_nom') }}"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('enquete_nom')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="enquete_age" class="block text-sm font-medium text-gray-700">ÂGE:</label>
                                        <input type="number" name="enquete_age" id="enquete_age" min="1" max="120"
                                            value="{{ old('enquete_age') }}"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('enquete_age')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">SEXE:</label>
                                        <div class="flex space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="enquete_sexe" value="M" {{ old('enquete_sexe') == 'M' ? 'checked' : '' }}
                                                    class="mr-2">
                                                Masculin
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="enquete_sexe" value="F" {{ old('enquete_sexe') == 'F' ? 'checked' : '' }}
                                                    class="mr-2">
                                                Féminin
                                            </label>
                                        </div>
                                        @error('enquete_sexe')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="enquete_fonction" class="block text-sm font-medium text-gray-700">FONCTION:</label>
                                    <input type="text" name="enquete_fonction" id="enquete_fonction"
                                        value="{{ old('enquete_fonction') }}"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('enquete_fonction')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="enquete_contact" class="block text-sm font-medium text-gray-700">TÉLÉPHONE/EMAIL:</label>
                                    <input type="text" name="enquete_contact" id="enquete_contact"
                                        value="{{ old('enquete_contact') }}"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('enquete_contact')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informations sur le patronyme -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 bg-gray-100 p-3 rounded">
                                II. INFORMATIONS SUR LE PATRONYME
                            </h3>

                            <div class="space-y-6">
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700">1. Patronyme:</label>
                                    <input type="text" name="nom" id="nom" required
                                        value="{{ old('nom') }}"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('nom')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="groupe_ethnique_id" class="block text-sm font-medium text-gray-700">2. Groupe ethnoculturel:</label>
                                    <select name="groupe_ethnique_id" id="groupe_ethnique_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez un groupe ethnique</option>
                                        @foreach($groupesEthniques as $groupe)
                                            <option value="{{ $groupe->id }}" {{ old('groupe_ethnique_id') == $groupe->id ? 'selected' : '' }}>
                                                {{ $groupe->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('groupe_ethnique_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="origine" class="block text-sm font-medium text-gray-700">3. Origine du patronyme:</label>
                                    <textarea name="origine" id="origine" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('origine') }}</textarea>
                                    @error('origine')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="signification" class="block text-sm font-medium text-gray-700">4. Signification du patronyme:</label>
                                    <textarea name="signification" id="signification" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('signification') }}</textarea>
                                    @error('signification')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="histoire" class="block text-sm font-medium text-gray-700">5. Histoire étiologique:</label>
                                    <textarea name="histoire" id="histoire" rows="4"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('histoire') }}</textarea>
                                    @error('histoire')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="langue_id" class="block text-sm font-medium text-gray-700">6. Langue parlée:</label>
                                    <select name="langue_id" id="langue_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez une langue</option>
                                        @foreach($langues as $langue)
                                            <option value="{{ $langue->id }}" {{ old('langue_id') == $langue->id ? 'selected' : '' }}>
                                                {{ $langue->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('langue_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">7. Comment ce patronyme se transmet-il ?</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="transmission" value="pere" {{ old('transmission') == 'pere' ? 'checked' : '' }}
                                                class="mr-2">
                                            a. Père en fils/fille
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="transmission" value="mere" {{ old('transmission') == 'mere' ? 'checked' : '' }}
                                                class="mr-2">
                                            b. Mère en fils/fille
                                        </label>
                                    </div>
                                    @error('transmission')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="patronyme_sexe" class="block text-sm font-medium text-gray-700">8. Les hommes et les femmes ont-ils le même patronyme ? Sinon quels sont-ils ?</label>
                                    <textarea name="patronyme_sexe" id="patronyme_sexe" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('patronyme_sexe') }}</textarea>
                                    @error('patronyme_sexe')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="totem" class="block text-sm font-medium text-gray-700">9. Existe-t-il un totem lié à votre patronyme ?</label>
                                    <input type="text" name="totem" id="totem"
                                        value="{{ old('totem') }}"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('totem')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="justification_totem" class="block text-sm font-medium text-gray-700">10. Qu'est-ce qui justifie ce totem ?</label>
                                    <textarea name="justification_totem" id="justification_totem" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('justification_totem') }}</textarea>
                                    @error('justification_totem')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="parents_plaisanterie" class="block text-sm font-medium text-gray-700">11. Quels sont vos parents à plaisanterie/alliances ?</label>
                                    <textarea name="parents_plaisanterie" id="parents_plaisanterie" rows="3"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('parents_plaisanterie') }}</textarea>
                                    @error('parents_plaisanterie')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Localisation -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 bg-gray-100 p-3 rounded">
                                III. LOCALISATION
                            </h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="region_id" class="block text-sm font-medium text-gray-700">Région</label>
                                        <select name="region_id" id="region_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Sélectionnez une région</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                                    {{ $region->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="province_id" class="block text-sm font-medium text-gray-700">Province</label>
                                        <select name="province_id" id="province_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Sélectionnez une province</option>
                                        </select>
                                        @error('province_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="commune_id" class="block text-sm font-medium text-gray-700">Commune</label>
                                    <select name="commune_id" id="commune_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez une commune</option>
                                    </select>
                                    @error('commune_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <a href="{{ route('patronymes.index') }}" class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                                <i class="mr-2 fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                <i class="mr-2 fas fa-save"></i> Enregistrer
                            </button>
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
            fetch(`/get-provinces?region_id=${regionId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.id}">${province.nom}</option>`;
                    });
                });
        });

        provinceSelect?.addEventListener('change', function () {
            const provinceId = this.value;
            communeSelect && (communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>');
            if (!provinceId) return;
            fetch(`/get-communes?province_id=${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(commune => {
                        communeSelect.innerHTML += `<option value="${commune.id}">${commune.nom}</option>`;
                    });
                });
        });
    });
    </script>
</x-app-layout>
