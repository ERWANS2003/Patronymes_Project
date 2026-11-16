<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gradient-bf">
                <i class="mr-2 fas fa-plus-circle"></i>{{ __('FICHE DE COLLECTE DES PATRONYMES NATIONAUX') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-bfNavy-600">Étape 2 sur 3</span>
                <div class="w-32 bg-bfNavy-200 rounded-full h-2">
                    <div class="bg-gradient-bf-flag h-2 rounded-full" style="width: 66%"></div>
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
                                <div class="w-8 h-8 bg-bfGreen-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                <span class="ml-2 text-sm font-medium text-bfGreen-600">Patronyme</span>
                            </div>
                            <div class="w-16 h-0.5 bg-bfNavy-200"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-bfNavy-200 text-bfNavy-600 rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                <span class="ml-2 text-sm font-medium text-bfNavy-600">Localisation</span>
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

                    <form action="{{ route('patronymes.create.step3') }}" method="POST">
                        @csrf

                        <!-- Section II: INFORMATIONS SUR LE PATRONYME -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gradient-bf mb-6 bg-gradient-to-r from-bfGold-50 to-bfRed-50 p-4 rounded-lg border border-bfGold-200">
                                <i class="fas fa-book mr-2 text-bfGold-600"></i>
                                II. INFORMATIONS SUR LE PATRONYME
                            </h3>

                            <div class="space-y-6">
                                <!-- Question 1 -->
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-signature mr-1 text-bfGold-600"></i>1. Patronyme:
                                    </label>
                                    <input type="text" name="nom" id="nom" required
                                        value="{{ old('nom', $patronymeData['nom'] ?? '') }}"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Nom du patronyme">
                                    @error('nom')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 2 -->
                                <div>
                                    <label for="groupe_ethnique_id" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-users mr-1 text-bfGold-600"></i>2. Groupe ethnoculturel:
                                    </label>
                                    <select name="groupe_ethnique_id" id="groupe_ethnique_id"
                                        class="form-select focus:ring-bfGold-500 focus:border-bfGold-500">
                                        <option value="">Sélectionnez un groupe ethnique</option>
                                        @foreach($groupesEthniques as $groupe)
                                            <option value="{{ $groupe->id }}" {{ old('groupe_ethnique_id', $patronymeData['groupe_ethnique_id'] ?? '') == $groupe->id ? 'selected' : '' }}>
                                                {{ $groupe->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('groupe_ethnique_id')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 3 -->
                                <div>
                                    <label for="origine" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-map-marker-alt mr-1 text-bfGold-600"></i>3. Origine du patronyme:
                                    </label>
                                    <textarea name="origine" id="origine" rows="3"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Décrivez l'origine géographique ou historique du patronyme">{{ old('origine', $patronymeData['origine'] ?? '') }}</textarea>
                                    @error('origine')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 4 -->
                                <div>
                                    <label for="signification" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-lightbulb mr-1 text-bfGold-600"></i>4. Signification du patronyme:
                                    </label>
                                    <textarea name="signification" id="signification" rows="3"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Expliquez la signification du patronyme">{{ old('signification', $patronymeData['signification'] ?? '') }}</textarea>
                                    @error('signification')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 5 -->
                                <div>
                                    <label for="histoire" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-history mr-1 text-bfGold-600"></i>5. Histoire étiologique:
                                    </label>
                                    <textarea name="histoire" id="histoire" rows="4"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Racontez l'histoire ou la légende associée au patronyme">{{ old('histoire', $patronymeData['histoire'] ?? '') }}</textarea>
                                    @error('histoire')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 6 -->
                                <div>
                                    <label for="langue_id" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-language mr-1 text-bfGold-600"></i>6. Langue parlée:
                                    </label>
                                    <select name="langue_id" id="langue_id"
                                        class="form-select focus:ring-bfGold-500 focus:border-bfGold-500">
                                        <option value="">Sélectionnez une langue</option>
                                        @foreach($langues as $langue)
                                            <option value="{{ $langue->id }}" {{ old('langue_id', $patronymeData['langue_id'] ?? '') == $langue->id ? 'selected' : '' }}>
                                                {{ $langue->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('langue_id')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 7 -->
                                <div>
                                    <label class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-exchange-alt mr-1 text-bfGold-600"></i>7. Comment ce patronyme se transmet-il ?
                                    </label>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-3 border border-bfNavy-200 rounded-lg hover:bg-bfGold-50 cursor-pointer">
                                            <input type="radio" name="transmission" value="pere" {{ old('transmission', $patronymeData['transmission'] ?? '') == 'pere' ? 'checked' : '' }}
                                                class="mr-3 text-bfGold-600 focus:ring-bfGold-500">
                                            <span class="text-bfNavy-700">a. Père en fils/fille</span>
                                        </label>
                                        <label class="flex items-center p-3 border border-bfNavy-200 rounded-lg hover:bg-bfGold-50 cursor-pointer">
                                            <input type="radio" name="transmission" value="mere" {{ old('transmission', $patronymeData['transmission'] ?? '') == 'mere' ? 'checked' : '' }}
                                                class="mr-3 text-bfGold-600 focus:ring-bfGold-500">
                                            <span class="text-bfNavy-700">b. Mère en fils/fille</span>
                                        </label>
                                    </div>
                                    @error('transmission')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 8 -->
                                <div>
                                    <label for="patronyme_sexe" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-venus-mars mr-1 text-bfGold-600"></i>8. Les hommes et les femmes ont-ils le même patronyme ?
                                    </label>
                                    <select name="patronyme_sexe" id="patronyme_sexe"
                                        class="form-select focus:ring-bfGold-500 focus:border-bfGold-500">
                                        <option value="">Sélectionner une réponse</option>
                                        <option value="oui" {{ old('patronyme_sexe', $patronymeData['patronyme_sexe'] ?? '') == 'oui' ? 'selected' : '' }}>Oui, même patronyme</option>
                                        <option value="non" {{ old('patronyme_sexe', $patronymeData['patronyme_sexe'] ?? '') == 'non' ? 'selected' : '' }}>Non, patronymes différents</option>
                                        <option value="autre" {{ old('patronyme_sexe', $patronymeData['patronyme_sexe'] ?? '') == 'autre' ? 'selected' : '' }}>Autre à préciser</option>
                                    </select>
                                    @error('patronyme_sexe')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror

                                    <!-- Zone de texte pour "Autre à préciser" -->
                                    <div id="patronyme_sexe_precision" style="display: {{ old('patronyme_sexe') == 'autre' ? 'block' : 'none' }};" class="mt-3">
                                        <label for="patronyme_sexe_detail" class="block text-sm font-medium text-bfNavy-700 mb-2">Précisez votre réponse :</label>
                                        <textarea name="patronyme_sexe_detail" id="patronyme_sexe_detail" rows="3"
                                            placeholder="Expliquez la situation particulière..."
                                            class="form-input focus:ring-bfGold-500 focus:border-bfGold-500">{{ old('patronyme_sexe_detail', $patronymeData['patronyme_sexe_detail'] ?? '') }}</textarea>
                                        @error('patronyme_sexe_detail')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Question 9 -->
                                <div>
                                    <label for="totem" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-paw mr-1 text-bfGold-600"></i>9. Existe-t-il un totem lié à votre patronyme ?
                                    </label>
                                    <input type="text" name="totem" id="totem"
                                        value="{{ old('totem', $patronymeData['totem'] ?? '') }}"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Nom du totem (animal, plante, etc.)">
                                    @error('totem')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 10 -->
                                <div>
                                    <label for="justification_totem" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-question-circle mr-1 text-bfGold-600"></i>10. Qu'est-ce qui justifie ce totem ?
                                    </label>
                                    <textarea name="justification_totem" id="justification_totem" rows="3"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Expliquez la raison ou l'histoire du totem">{{ old('justification_totem', $patronymeData['justification_totem'] ?? '') }}</textarea>
                                    @error('justification_totem')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question 11 -->
                                <div>
                                    <label for="parents_plaisanterie" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-handshake mr-1 text-bfGold-600"></i>11. Quels sont vos parents à plaisanterie/alliances ?
                                    </label>
                                    <textarea name="parents_plaisanterie" id="parents_plaisanterie" rows="3"
                                        class="form-input focus:ring-bfGold-500 focus:border-bfGold-500"
                                        placeholder="Décrivez les relations de plaisanterie ou alliances">{{ old('parents_plaisanterie', $patronymeData['parents_plaisanterie'] ?? '') }}</textarea>
                                    @error('parents_plaisanterie')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('patronymes.create.step1') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i> Précédent
                            </a>
                            <button type="submit" class="btn btn-primary shadow-glow-bf">
                                Suivant <i class="ml-2 fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gestion de l'affichage de la zone de texte pour "Autre à préciser"
        const patronymeSexeSelect = document.querySelector('select[name="patronyme_sexe"]');
        const patronymeSexePrecision = document.getElementById('patronyme_sexe_precision');

        patronymeSexeSelect?.addEventListener('change', function () {
            if (this.value === 'autre') {
                patronymeSexePrecision.style.display = 'block';
            } else {
                patronymeSexePrecision.style.display = 'none';
            }
        });
    });
    </script>
</x-app-layout>
