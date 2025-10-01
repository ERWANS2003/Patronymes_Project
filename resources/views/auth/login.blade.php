<x-app-layout>
    <x-slot name="header">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-sign-in-alt text-blue-600 mr-2"></i>
                Connexion
            </h1>
            <p class="text-gray-600 mt-1">
                Accédez à votre compte pour contribuer au répertoire
            </p>
        </div>
    </x-slot>

    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="card p-8">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-white text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    Connexion à votre compte
                </h2>
                <p class="text-gray-600">
                    Ou
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        créez un nouveau compte
                    </a>
                </p>
            </div>

            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

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
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="form-input"
                           placeholder="Votre mot de passe">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="block ml-2 text-sm text-gray-900">
                        Se souvenir de moi
                    </label>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
