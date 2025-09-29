<x-app-layout>
    @extends('layouts.app')
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-edit"></i>{{ __('Modifier le patronyme') }} : {{ $patronyme->nom }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('patronymes.update', $patronyme) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="nom" id="nom" required
                                    value="{{ old('nom', $patronyme->nom) }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nom')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="origine" class="block text-sm font-medium text-gray-700">Origine</label>
                                <input type="text" name="origine" id="origine"
                                    value="{{ old('origine', $patronyme->origine) }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('origine')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="signification" class="block text-sm font-medium text-gray-700">Signification</label>
                                <textarea name="signification" id="signification" rows="3"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('signification', $patronyme->signification) }}</textarea>
                                @error('signification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="histoire" class="block text-sm font-medium text-gray-700">Histoire</label>
                                <textarea name="histoire" id="histoire" rows="5"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('histoire', $patronyme->histoire) }}</textarea>
                                @error('histoire')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="region_id" class="block text-sm font-medium text-gray-700">Région</label>
                                    <select name="region_id" id="region_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez une région</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id', $patronyme->region_id) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="departement_id" class="block text-sm font-medium text-gray-700">Département</label>
                                    <select name="departement_id" id="departement_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez un département</option>
                                    </select>
                                    @error('departement_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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

                            <div>
                                <label for="frequence" class="block text-sm font-medium text-gray-700">Fréquence</label>
                                <input type="number" min="0" name="frequence" id="frequence"
                                    value="{{ old('frequence', $patronyme->frequence) }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('frequence')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6 space-x-3">
                            <a href="{{ route('patronymes.show', $patronyme) }}" class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                                <i class="mr-2 fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                <i class="mr-2 fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const regionSelect = document.querySelector('select[name="region_id"]');
    const departementSelect = document.querySelector('select[name="departement_id"]');
    const provinceSelect = document.querySelector('select[name="province_id"]');
    const communeSelect = document.querySelector('select[name="commune_id"]');

    regionSelect?.addEventListener('change', function () {
        const regionId = this.value;
        if (!departementSelect) return;
        departementSelect.innerHTML = '<option value="">Sélectionnez un département</option>';
        provinceSelect && (provinceSelect.innerHTML = '<option value="">Sélectionnez une province</option>');
        communeSelect && (communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>');
        if (!regionId) return;
        fetch(`/api/departements?region_id=${regionId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(dep => {
                    departementSelect.innerHTML += `<option value="${dep.id}">${dep.name}</option>`;
                });
            });
    });

    departementSelect?.addEventListener('change', function () {
        const regionId = regionSelect?.value;
        if (!regionId || !provinceSelect) return;
        provinceSelect.innerHTML = '<option value="">Sélectionnez une province</option>';
        communeSelect && (communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>');
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
        if (!provinceId || !communeSelect) return;
        communeSelect.innerHTML = '<option value="">Sélectionnez une commune</option>';
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
