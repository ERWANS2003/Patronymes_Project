<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                Inscription
            </h1>
            <p class="text-gray-600 mt-1">
                Créez votre compte pour contribuer au répertoire
            </p>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Créer votre compte
                </h2>
                <p class="text-gray-600">
                    Ou
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        connectez-vous à votre compte existant
                    </a>
                </p>
            </div>

            <form class="space-y-6" action="{{ route('register') }}" method="POST">
                @csrf

                <div>
                    <label for="name" class="form-label">Nom complet</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="form-input"
                           placeholder="Votre nom complet"
                           value="{{ old('name') }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="form-input"
                           placeholder="votre@email.com"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="form-input"
                           placeholder="Votre mot de passe">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="form-input"
                           placeholder="Confirmer le mot de passe">
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="terms" class="block ml-2 text-sm text-gray-900">
                        J'accepte les
                        <a href="#" class="text-blue-600 hover:text-blue-500">conditions d'utilisation</a>
                        et la
                        <a href="#" class="text-blue-600 hover:text-blue-500">politique de confidentialité</a>
                    </label>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer mon compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
