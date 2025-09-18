<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Répertoire des Patronymes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-indigo-600">
                            <i class="mr-2 fas fa-book-open"></i>Patronymes
                        </h1>
                    </div>
                    <nav class="hidden md:ml-6 md:flex md:space-x-4">
                        <a href="/dashboard" class="px-3 py-2 text-sm font-medium text-gray-900">Tableau de bord</a>
                        <a href="/patronymes" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Patronymes</a>
                        <a href="/contributions" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Mes contributions</a>
                    </nav>
                </div>
                <div class="flex items-center">
                    <div class="relative ml-3">
                        <div>
                            <button type="button" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Ouvrir le menu utilisateur</span>
                                <div class="flex items-center justify-center w-8 h-8 bg-indigo-600 rounded-full">
                                    <span class="font-medium text-white">U</span>
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700">Utilisateur</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-10">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="mb-8 overflow-hidden bg-indigo-600 rounded-lg shadow-sm">
                <div class="px-6 py-12 text-center">
                    <h1 class="text-3xl font-bold text-white">Bienvenue sur votre tableau de bord</h1>
                    <p class="mt-2 text-indigo-100">Découvrez et contribuez à notre répertoire des patronymes</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                <!-- Patronymes consultés -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                                <i class="text-2xl text-white fas fa-eye"></i>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Patronymes consultés</dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">24</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contributions -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-500 rounded-md">
                                <i class="text-2xl text-white fas fa-edit"></i>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Contributions</dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">3</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commentaires -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-blue-500 rounded-md">
                                <i class="text-2xl text-white fas fa-comments"></i>
                            </div>
                            <div class="flex-1 w-0 ml-5">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Commentaires</dt>
                                    <dd>
                                        <div class="text-lg font-medium text-gray-900">7</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
                <!-- Recherche rapide -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Recherche rapide</h3>
                        <form class="space-y-4">
                            <div>
                                <input type="text" placeholder="Rechercher un patronyme..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                <i class="mr-2 fas fa-search"></i> Rechercher
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Actions rapides</h3>
                        <div class="space-y-3">
                            <a href="/patronymes/create" class="block w-full px-4 py-2 text-center text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                <i class="mr-2 fas fa-plus"></i> Ajouter un patronyme
                            </a>
                            <a href="/contributions" class="block w-full px-4 py-2 text-center text-white bg-green-600 rounded-md hover:bg-green-700">
                                <i class="mr-2 fas fa-edit"></i> Mes contributions
                            </a>
                            <a href="/profile" class="block w-full px-4 py-2 text-center text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <i class="mr-2 fas fa-user"></i> Mon profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières activités -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Dernières activités</h3>
                    <div class="space-y-4">
                        <!-- Activité 1 -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full">
                                    <i class="text-green-600 fas fa-check"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Contribution validée</p>
                                <p class="text-sm text-gray-500">Votre contribution pour "Diallo" a été approuvée</p>
                                <p class="text-xs text-gray-400">Il y a 2 heures</p>
                            </div>
                        </div>

                        <!-- Activité 2 -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full">
                                    <i class="text-blue-600 fas fa-comment"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Nouveau commentaire</p>
                                <p class="text-sm text-gray-500">Vous avez commenté "Traoré"</p>
                                <p class="text-xs text-gray-400">Il y a 1 jour</p>
                            </div>
                        </div>

                        <!-- Activité 3 -->
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-100 rounded-full">
                                    <i class="text-indigo-600 fas fa-search"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Recherche effectuée</p>
                                <p class="text-sm text-gray-500">Vous avez recherché "patronymes bambara"</p>
                                <p class="text-xs text-gray-400">Il y a 2 jours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-20 bg-white border-t border-gray-200">
        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">&copy; 2023 Répertoire des Patronymes. Tous droits réservés.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
