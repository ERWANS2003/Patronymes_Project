<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Répertoire des Patronymes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex items-center justify-center min-h-screen px-4 py-12 bg-gray-50 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="flex-shrink-0">
                    <i class="text-5xl text-indigo-600 fas fa-book-open"></i>
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-center text-gray-900">
                Créer votre compte
            </h2>
            <p class="mt-2 text-sm text-center text-gray-600">
                Ou
                <a href="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                    connectez-vous à votre compte existant
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="/register" method="POST">
            @csrf
            <div class="space-y-4 rounded-md shadow-sm">
                <div>
                    <label for="name" class="block mb-1 text-sm font-medium text-gray-700">Nom complet</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Votre nom complet">
                </div>

                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Adresse email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Adresse email">
                </div>

                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Mot de passe">
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-md appearance-none focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Confirmer le mot de passe">
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="terms" class="block ml-2 text-sm text-gray-900">
                    J'accepte les
                    <a href="#" class="text-indigo-600 hover:text-indigo-500">conditions d'utilisation</a>
                    et la
                    <a href="#" class="text-indigo-600 hover:text-indigo-500">politique de confidentialité</a>
                </label>
            </div>

            <div>
                <button type="submit"
                        class="relative flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md group hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="text-indigo-500 fas fa-user-plus group-hover:text-indigo-400"></i>
                    </span>
                    Créer mon compte
                </button>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 text-gray-500 bg-gray-50">
                            Ou inscrivez-vous avec
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-6">
                    <div>
                        <a href="#" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            <i class="text-red-500 fab fa-google"></i>
                            <span class="ml-2">Google</span>
                        </a>
                    </div>

                    <div>
                        <a href="#" class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            <i class="text-blue-600 fab fa-facebook-f"></i>
                            <span class="ml-2">Facebook</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
