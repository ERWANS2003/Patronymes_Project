<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gradient-bf">
                <i class="mr-2 fas fa-plus-circle"></i>{{ __('FICHE DE COLLECTE DES PATRONYMES NATIONAUX') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-bfNavy-600">Étape 1 sur 3</span>
                <div class="w-32 bg-bfNavy-200 rounded-full h-2">
                    <div class="bg-gradient-bf-flag h-2 rounded-full" style="width: 33%"></div>
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
                                <div class="w-8 h-8 bg-bfGreen-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                <span class="ml-2 text-sm font-medium text-bfGreen-600">Identité</span>
                            </div>
                            <div class="w-16 h-0.5 bg-bfNavy-200"></div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-bfNavy-200 text-bfNavy-600 rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                <span class="ml-2 text-sm font-medium text-bfNavy-600">Patronyme</span>
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

                    <form action="{{ route('patronymes.create.step2') }}" method="POST">
                        @csrf


                        <!-- Section I: IDENTITÉ DE L'ENQUÊTÉ -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gradient-bf mb-6 bg-gradient-to-r from-bfGreen-50 to-bfGold-50 p-4 rounded-lg border border-bfGreen-200">
                                <i class="fas fa-user mr-2 text-bfGreen-600"></i>
                                I. IDENTITÉ DE L'ENQUÊTÉ
                            </h3>

                            <div class="grid grid-cols-1 gap-6">

                                <!-- NOM ET PRÉNOM -->
                                <div>
                                    <label for="enquete_nom" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-id-card mr-1 text-bfGreen-600"></i>NOM ET PRÉNOM:
                                    </label>
                                    <input type="text" name="enquete_nom" id="enquete_nom" required
                                        value="{{ old('enquete_nom', $patronymeData['enquete_nom'] ?? '') }}"
                                        class="form-input focus:ring-bfGreen-500 focus:border-bfGreen-500"
                                        placeholder="Nom complet de l'enquêté">
                                    @error('enquete_nom')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- ÂGE et SEXE -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="enquete_age" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-birthday-cake mr-1 text-bfGreen-600"></i>ÂGE:
                                        </label>
                                        <input type="number" name="enquete_age" id="enquete_age" min="1" max="120"
                                            value="{{ old('enquete_age', $patronymeData['enquete_age'] ?? '') }}"
                                            class="form-input focus:ring-bfGreen-500 focus:border-bfGreen-500"
                                            placeholder="Âge de l'enquêté">
                                        @error('enquete_age')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-venus-mars mr-1 text-bfGreen-600"></i>SEXE:
                                        </label>
                                        <div class="flex space-x-6">
                                            <label class="flex items-center">
                                                <input type="radio" name="enquete_sexe" value="M" {{ old('enquete_sexe', $patronymeData['enquete_sexe'] ?? '') == 'M' ? 'checked' : '' }}
                                                    class="mr-2 text-bfGreen-600 focus:ring-bfGreen-500">
                                                <span class="text-bfNavy-700">Masculin</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="enquete_sexe" value="F" {{ old('enquete_sexe', $patronymeData['enquete_sexe'] ?? '') == 'F' ? 'checked' : '' }}
                                                    class="mr-2 text-bfGreen-600 focus:ring-bfGreen-500">
                                                <span class="text-bfNavy-700">Féminin</span>
                                            </label>
                                        </div>
                                        @error('enquete_sexe')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- FONCTION -->
                                <div>
                                    <label for="enquete_fonction" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                        <i class="fas fa-briefcase mr-1 text-bfGreen-600"></i>FONCTION:
                                    </label>
                                    <input type="text" name="enquete_fonction" id="enquete_fonction"
                                        value="{{ old('enquete_fonction', $patronymeData['enquete_fonction'] ?? '') }}"
                                        class="form-input focus:ring-bfGreen-500 focus:border-bfGreen-500"
                                        placeholder="Fonction ou profession de l'enquêté">
                                    @error('enquete_fonction')
                                        <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- TÉLÉPHONE et EMAIL -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="enquete_telephone" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-phone mr-1 text-bfGreen-600"></i>TÉLÉPHONE:
                                        </label>
                                        <input type="tel" name="enquete_telephone" id="enquete_telephone"
                                            value="{{ old('enquete_telephone', $patronymeData['enquete_telephone'] ?? '') }}"
                                            class="form-input focus:ring-bfGreen-500 focus:border-bfGreen-500"
                                            placeholder="Numéro de téléphone">
                                        @error('enquete_telephone')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="enquete_email" class="block text-sm font-medium text-bfNavy-700 mb-2">
                                            <i class="fas fa-envelope mr-1 text-bfGreen-600"></i>EMAIL:
                                        </label>
                                        <input type="email" name="enquete_email" id="enquete_email"
                                            value="{{ old('enquete_email', $patronymeData['enquete_email'] ?? '') }}"
                                            class="form-input focus:ring-bfGreen-500 focus:border-bfGreen-500"
                                            placeholder="Adresse email">
                                        @error('enquete_email')
                                            <p class="mt-1 text-sm text-bfRed-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8">
                            <a href="{{ route('patronymes.index') }}" class="btn btn-secondary">
                                <i class="mr-2 fas fa-arrow-left"></i> Retour à la liste
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
</x-app-layout>
