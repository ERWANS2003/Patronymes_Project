<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-plus-circle"></i>{{ __('Ajouter un nouveau patronyme') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('patronymes.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom *</label>
                                <input type="text" name="nom" id="nom" required
                                    value="{{ old('nom') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('nom')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="origine" class="block text-sm font-medium text-gray-700">Origine</label>
                                <input type="text" name="origine" id="origine"
                                    value="{{ old('origine') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('origine')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="signification" class="block text-sm font-medium text-gray-700">Signification</label>
                                <textarea name="signification" id="signification" rows="3"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('signification') }}</textarea>
                                @error('signification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="histoire" class="block text-sm font-medium text-gray-700">Histoire</label>
                                <textarea name="histoire" id="histoire" rows="5"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('histoire') }}</textarea>
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
                                    <label for="groupe_ethnique_id" class="block text-sm font-medium text-gray-700">Groupe ethnique</label>
                                    <select name="groupe_ethnique_id" id="groupe_ethnique_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez un groupe</option>
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
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label for="langue_id" class="block text-sm font-medium text-gray-700">Langue</label>
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
                                    <label for="mode_transmission_id" class="block text-sm font-medium text-gray-700">Mode de transmission</label>
                                    <select name="mode_transmission_id" id="mode_transmission_id"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Sélectionnez un mode</option>
                                        @foreach($modesTransmission as $mode)
                                            <option value="{{ $mode->id }}" {{ old('mode_transmission_id') == $mode->id ? 'selected' : '' }}>
                                                {{ $mode->type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('mode_transmission_id')
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
</x-app-layout>
