<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répertoire des Patronymes - Découvrez l'origine des noms de famille</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-indigo-600">
                            <i class="mr-2 fas fa-book-open"></i>Patronymes
                        </h1>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="#" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-900 border-b-2 border-indigo-500">
                            Accueil
                        </a>
                        <a href="#features" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            Fonctionnalités
                        </a>
                        <a href="#about" class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:border-gray-300 hover:text-gray-700">
                            À propos
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="/login" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        Connexion
                    </a>
                    <a href="/register" class="px-4 py-2 ml-4 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Inscription
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <main>
        <div class="relative overflow-hidden bg-white">
            <div class="mx-auto max-w-7xl">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <div class="px-4 pt-10 mx-auto max-w-7xl sm:pt-12 sm:px-6 md:pt-16 lg:pt-20 lg:px-8 xl:pt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block">Découvrez l'histoire</span>
                                <span class="block text-indigo-600">de votre nom de famille</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Explorez notre répertoire complet des patronymes, leurs origines, significations et histoires à travers les régions et les cultures.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="/register" class="flex items-center justify-center w-full px-8 py-3 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                        Commencer l'exploration
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#search" class="flex items-center justify-center w-full px-8 py-3 text-base font-medium text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                        <i class="mr-2 fas fa-search"></i> Rechercher
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <div class="flex items-center justify-center w-full h-56 bg-indigo-50 sm:h-72 md:h-96 lg:w-full lg:h-full">
                    <div class="text-center">
                        <i class="text-indigo-200 fas fa-globe-africa text-9xl"></i>
                        <p class="mt-4 text-lg text-indigo-300">Votre histoire commence ici</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <section id="search" class="py-12 bg-gray-50">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">Recherche</h2>
                    <p class="mt-2 text-3xl font-extrabold leading-8 tracking-tight text-gray-900 sm:text-4xl">
                        Trouvez votre patronyme
                    </p>
                    <p class="max-w-2xl mt-4 text-xl text-gray-500 lg:mx-auto">
                        Recherchez par nom, région, ethnie ou signification
                    </p>
                </div>

                <div class="p-6 mt-10 bg-white rounded-lg shadow-sm">
                    <form action="/search" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom du patronyme</label>
                                <input type="text" name="nom" placeholder="Ex: Dupont, Martin..."
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Région</label>
                                <select name="region_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les régions</option>
                                    <option value="1">Île-de-France</option>
                                    <option value="2">Provence-Alpes-Côte d'Azur</option>
                                    <option value="3">Auvergne-Rhône-Alpes</option>
                                    <option value="4">Occitanie</option>
                                    <option value="5">Hauts-de-France</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Groupe ethnique</label>
                                <select name="groupe_ethnique_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les groupes</option>
                                    <option value="1">Peuls</option>
                                    <option value="2">Bambaras</option>
                                    <option value="3">Wolofs</option>
                                    <option value="4">Touaregs</option>
                                    <option value="5">Soninkés</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <i class="mr-2 fas fa-search"></i> Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-12 bg-white">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">Fonctionnalités</h2>
                    <p class="mt-2 text-3xl font-extrabold leading-8 tracking-tight text-gray-900 sm:text-4xl">
                        Une plateforme complète
                    </p>
                    <p class="max-w-2xl mt-4 text-xl text-gray-500 lg:mx-auto">
                        Découvrez toutes les fonctionnalités qui font de notre répertoire l'outil ultime
                    </p>
                </div>

                <div class="mt-10">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                        <!-- Feature 1 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 text-white bg-indigo-500 rounded-md">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium leading-6 text-gray-900">Base de données exhaustive</h4>
                                <p class="mt-2 text-base text-gray-500">
                                    Accédez à des milliers de patronymes avec leurs origines, significations et histoires détaillées.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 text-white bg-indigo-500 rounded-md">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium leading-6 text-gray-900">Recherche géographique</h4>
                                <p class="mt-2 text-base text-gray-500">
                                    Explorez les patronymes par région, province et commune pour découvrir leur répartition.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 text-white bg-indigo-500 rounded-md">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium leading-6 text-gray-900">Origines ethniques</h4>
                                <p class="mt-2 text-base text-gray-500">
                                    Comprenez les liens entre les patronymes et les différents groupes ethniques.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 4 -->
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center w-12 h-12 text-white bg-indigo-500 rounded-md">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium leading-6 text-gray-900">Contributions communautaires</h4>
                                <p class="mt-2 text-base text-gray-500">
                                    Ajoutez vos connaissances et partagez l'histoire de votre famille avec la communauté.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-12 bg-indigo-600">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base font-semibold tracking-wide text-indigo-200 uppercase">Chiffres clés</h2>
                    <p class="mt-2 text-3xl font-extrabold leading-8 tracking-tight text-white sm:text-4xl">
                        Notre répertoire en quelques chiffres
                    </p>
                </div>

                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Stat 1 -->
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                                        <i class="text-2xl text-white fas fa-signature"></i>
                                    </div>
                                    <div class="flex-1 w-0 ml-5">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Patronymes recensés</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900">12,458</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                                        <i class="text-2xl text-white fas fa-globe-africa"></i>
                                    </div>
                                    <div class="flex-1 w-0 ml-5">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Régions couvertes</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900">15</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                                        <i class="text-2xl text-white fas fa-users"></i>
                                    </div>
                                    <div class="flex-1 w-0 ml-5">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Groupes ethniques</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900">28</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat 4 -->
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 p-3 bg-indigo-500 rounded-md">
                                        <i class="text-2xl text-white fas fa-user-edit"></i>
                                    </div>
                                    <div class="flex-1 w-0 ml-5">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Contributeurs actifs</dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900">1,247</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-12 bg-white">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base font-semibold tracking-wide text-indigo-600 uppercase">À propos</h2>
                    <p class="mt-2 text-3xl font-extrabold leading-8 tracking-tight text-gray-900 sm:text-4xl">
                        Notre mission
                    </p>
                </div>

                <div class="mx-auto mt-10 prose prose-lg text-gray-500 prose-indigo">
                    <p>
                        Le Répertoire des Patronymes a été créé dans le but de préserver et de partager la richesse
                        culturelle et historique des noms de famille à travers les régions et les communautés.
                    </p>
                    <p>
                        Notre plateforme collaborative permet à chacun de contribuer à cette grande œuvre collective
                        de préservation de notre patrimoine onomastique. Que vous soyez chercheur, généalogiste ou
                        simplement curieux de découvrir l'origine de votre nom, vous trouverez ici une communauté
                        passionnée et des ressources précieuses.
                    </p>
                    <p>
                        Rejoignez-nous dans cette aventure passionnante à la découverte de nos racines et de notre
                        histoire commune à travers les noms qui nous identifient.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-300 uppercase">Répertoire des Patronymes</h3>
                    <p class="mt-4 text-base text-gray-400">
                        Découvrez l'histoire et l'origine des noms de famille à travers les régions et les cultures.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-300 uppercase">Navigation</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="text-base text-gray-400 hover:text-white">Accueil</a></li>
                        <li><a href="#search" class="text-base text-gray-400 hover:text-white">Recherche</a></li>
                        <li><a href="#features" class="text-base text-gray-400 hover:text-white">Fonctionnalités</a></li>
                        <li><a href="#about" class="text-base text-gray-400 hover:text-white">À propos</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-300 uppercase">Compte</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="/login" class="text-base text-gray-400 hover:text-white">Connexion</a></li>
                        <li><a href="/register" class="text-base text-gray-400 hover:text-white">Inscription</a></li>
                        <li><a href="#" class="text-base text-gray-400 hover:text-white">Mot de passe oublié</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold tracking-wider text-gray-300 uppercase">Contact</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="mailto:contact@patronymes.org" class="text-base text-gray-400 hover:text-white">contact@patronymes.org</a></li>
                        <li><a href="#" class="text-base text-gray-400 hover:text-white">Support</a></li>
                        <li><a href="#" class="text-base text-gray-400 hover:text-white">Contribuer</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-8 border-t border-gray-700 md:flex md:items-center md:justify-between">
                <div class="flex space-x-6 md:order-2">
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-300">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
                <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
                    &copy; 2023 Répertoire des Patronymes. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
