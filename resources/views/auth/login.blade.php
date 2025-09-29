<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Répertoire des Patronymes</title>
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
                Connexion à votre compte
            </h2>
            <p class="mt-2 text-sm text-center text-gray-600">
                Ou
                <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
                    créez un nouveau compte
                </a>
            </p>
        </div>

        <form class="mt-8 space-y-6" action="/login" method="POST">
            @csrf
            <div class="-space-y-px rounded-md shadow-sm">
                <div>
                    <label for="email" class="sr-only">Adresse email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-none appearance-none rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Adresse email">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="relative block w-full px-3 py-2 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-none appearance-none rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                           placeholder="Mot de passe">
                </div>
            </div>

            <div class="flex items-center">
                <input id="remember-me" name="remember-me" type="checkbox"
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="remember-me" class="block ml-2 text-sm text-gray-900">
                    Se souvenir de moi
                </label>
            </div>

            <div>
                <button type="submit"
                        class="relative flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md group hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="text-indigo-500 fas fa-lock group-hover:text-indigo-400"></i>
                    </span>
                    Se connecter
                </button>
            </div>

        </form>
    </div>
</body>
</html>
