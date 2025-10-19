<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Découvrez l'origine, la signification et l'histoire des patronymes du Burkina Faso. Base de données complète et interactive pour explorer notre patrimoine culturel.">
    <meta name="keywords" content="patronymes, Burkina Faso, noms de famille, origine, signification, histoire, culture, patrimoine">
    <meta name="author" content="Répertoire des Patronymes">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ config('app.name', 'Répertoire des Patronymes') }}">
    <meta property="og:description" content="Découvrez l'origine et la signification des patronymes du Burkina Faso">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ config('app.name', 'Répertoire des Patronymes') }}">
    <meta name="twitter:description" content="Découvrez l'origine et la signification des patronymes du Burkina Faso">
    <meta name="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <title>{{ config('app.name', 'Répertoire des Patronymes') }}</title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
</head>

<body class="h-full bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-bfGreen-600 to-bfRed-600 rounded-lg flex items-center justify-center ring-2 ring-bfGold-400/60">
                            <i class="fas fa-book text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">Patronymes BF</span>
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('patronymes.index') }}" class="nav-link">Explorer</a>
                    <a href="{{ route('patronymes.index') }}?featured=1" class="nav-link">Populaires</a>
                    <a href="#about" class="nav-link">À propos</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Connexion</a>
                        <a href="{{ route('register') }}" class="btn btn-danger">
                            <i class="fas fa-user-plus mr-2"></i>S'inscrire
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('patronymes.index') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">Explorer</a>
                <a href="{{ route('patronymes.index') }}?featured=1" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">Populaires</a>
                <a href="#about" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">À propos</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">Connexion</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">S'inscrire</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero gradient-primary">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="hero-content">
            <div class="animate-fade-in-up">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Découvrez l'Histoire des
                    <span class="text-gradient bg-white bg-clip-text text-transparent">Patronymes</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
                    Explorez l'origine, la signification et l'histoire fascinante des noms de famille du Burkina Faso.
                    Une base de données complète pour préserver notre patrimoine culturel.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="{{ route('patronymes.index') }}" class="btn btn-primary text-lg px-8 py-4 shadow-glow">
                        <i class="fas fa-search mr-2"></i>Explorer les patronymes
                    </a>
                    <a href="{{ route('patronymes.index') }}?featured=1" class="btn btn-secondary text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-bfGreen-600">
                        <i class="fas fa-fire mr-2"></i>Patronymes populaires
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-bfGreen-600">
                            <i class="fas fa-user-plus mr-2"></i>Rejoindre la communauté
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-bfGreen-600">
                            <i class="fas fa-tachometer-alt mr-2"></i>Mon tableau de bord
                        </a>
                    @endguest
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                        <div class="stats-counter" data-count="{{ \App\Models\Patronyme::count() }}">0</div>
                        <p class="text-white/80 text-sm">Patronymes</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                        <div class="stats-counter" data-count="{{ \App\Models\Region::count() }}">0</div>
                        <p class="text-white/80 text-sm">Régions</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                        <div class="stats-counter" data-count="{{ \App\Models\GroupeEthnique::count() }}">0</div>
                        <p class="text-white/80 text-sm">Groupes ethniques</p>
                    </div>
                    <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                        <div class="stats-counter" data-count="{{ \App\Models\Langue::count() }}">0</div>
                        <p class="text-white/80 text-sm">Langues</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Recherche Rapide
                </h2>
                <p class="text-lg text-gray-600">
                    Trouvez instantanément l'origine et la signification d'un patronyme
                </p>
            </div>

            <div class="search-form">
                <form action="{{ route('patronymes.index') }}" method="GET" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="form-label">Nom du patronyme</label>
                            <input
                                type="text"
                                name="search"
                                placeholder="Ex: Traoré, Ouédraogo, Sawadogo..."
                                class="form-input"
                                value="{{ request('search') }}"
                            >
                        </div>
                        <div class="md:w-48">
                            <label class="form-label">Région</label>
                            <select name="region_id" class="form-select">
                                <option value="">Toutes les régions</option>
                                @foreach(\App\Models\Region::all() as $region)
                                    <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                        {{ $region->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:w-48">
                            <label class="form-label">Groupe ethnique</label>
                            <select name="groupe_ethnique_id" class="form-select">
                                <option value="">Tous les groupes</option>
                                @foreach(\App\Models\GroupeEthnique::all() as $groupe)
                                    <option value="{{ $groupe->id }}" {{ request('groupe_ethnique_id') == $groupe->id ? 'selected' : '' }}>
                                        {{ $groupe->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="btn btn-bfGreen-600 hover:bg-bfGreen-700 text-white px-8 py-3">
                            <i class="fas fa-search mr-2"></i>Rechercher
                        </button>
                    </div>
                </form>
            </div>

            <!-- Real-time Search Results -->
            <div id="welcome-real-time-results" class="hidden mt-8">
                <!-- Real-time search results will be displayed here -->
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    Fonctionnalités
                </h2>
                <p class="text-lg text-gray-600">
                    Une plateforme complète pour explorer le patrimoine patronymique
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="w-16 h-16 bg-bfGreen-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-2xl text-bfGreen-700"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Base de Données Complète</h3>
                    <p class="text-gray-600 mb-4">
                        Accédez à des milliers de patronymes avec leurs origines, significations et histoires détaillées.
                    </p>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-bfGreen-600 hover:bg-bfGreen-700 text-white text-sm">
                        <i class="fas fa-search mr-2"></i>Explorer
                    </a>
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-bfGold-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-2xl text-bfGold-700"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Recherche Avancée</h3>
                    <p class="text-gray-600 mb-4">
                        Filtrez par région, groupe ethnique, langue et bien d'autres critères pour des résultats précis.
                    </p>
                    <a href="{{ route('statistics.index') }}" class="btn btn-bfGold-600 hover:bg-bfGold-700 text-white text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="w-16 h-16 bg-bfGreen-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-bfGreen-700"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Communauté Active</h3>
                    <p class="text-gray-600 mb-4">
                        Contribuez à l'enrichissement de la base de données et partagez vos connaissances.
                    </p>
                    @auth
                        <a href="{{ route('patronymes.create') }}" class="btn btn-bfPurple-600 hover:bg-bfPurple-700 text-white text-sm">
                            <i class="fas fa-plus mr-2"></i>Contribuer
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-bfRed-600 hover:bg-bfRed-700 text-white text-sm">
                            <i class="fas fa-user-plus mr-2"></i>S'inscrire
                        </a>
                    @endauth
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-bfRed-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-2xl text-bfRed-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Favoris Personnalisés</h3>
                    <p class="text-gray-600 mb-4">
                        Sauvegardez vos patronymes préférés et créez votre collection personnelle.
                    </p>
                    @auth
                        <a href="{{ route('favorites.index') }}" class="btn btn-secondary text-sm">
                            <i class="fas fa-heart mr-2"></i>Mes favoris
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline text-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                        </a>
                    @endauth
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.5s">
                    <div class="w-16 h-16 bg-bfGreen-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-bfGreen-700"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Interface Responsive</h3>
                    <p class="text-gray-600 mb-4">
                        Accédez à la plateforme depuis n'importe quel appareil avec une expérience optimisée.
                    </p>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-outline text-sm">
                        <i class="fas fa-mobile-alt mr-2"></i>Essayer
                    </a>
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.6s">
                    <div class="w-16 h-16 bg-bfGold-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-2xl text-bfGold-700"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Partage Social</h3>
                    <p class="text-gray-600 mb-4">
                        Partagez vos découvertes sur les réseaux sociaux et faites découvrir notre patrimoine.
                    </p>
                    <button onclick="shareWebsite()" class="btn btn-indigo text-sm">
                        <i class="fas fa-share-alt mr-2"></i>Partager
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-bfGreen-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Notre Impact
                </h2>
                <p class="text-xl text-bfGreen-100">
                    Des chiffres qui témoignent de notre engagement pour la préservation du patrimoine
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\Patronyme::count() }}">0</div>
                    <p class="text-bfGreen-100">Patronymes enregistrés</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\User::count() }}">0</div>
                    <p class="text-bfGreen-100">Utilisateurs actifs</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\Region::count() }}">0</div>
                    <p class="text-bfGreen-100">Régions couvertes</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\GroupeEthnique::count() }}">0</div>
                    <p class="text-bfGreen-100">Groupes ethniques</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Prêt à explorer ?
                </h2>
                <p class="text-lg text-gray-600 mb-8">
                    Découvrez dès maintenant l'histoire fascinante des patronymes du Burkina Faso
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('patronymes.index') }}" class="btn btn-primary text-lg px-8 py-4 shadow-glow">
                        <i class="fas fa-search mr-2"></i>Commencer l'exploration
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline text-lg px-8 py-4">
                            <i class="fas fa-user-plus mr-2"></i>Rejoindre la communauté
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-bold">Patronymes BF</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Découvrez et explorez l'histoire fascinante des patronymes du Burkina Faso.
                        Une plateforme dédiée à la préservation de notre patrimoine culturel.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Navigation</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('patronymes.index') }}" class="text-gray-400 hover:text-white transition-colors">Explorer</a></li>
                        <li><a href="{{ route('patronymes.index') }}?featured=1" class="text-gray-400 hover:text-white transition-colors">Populaires</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; {{ date('Y') }} Répertoire des Patronymes. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>

    <!-- Counter Animation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stats-counter');

            const animateCounter = (counter) => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 16);
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });

            counters.forEach(counter => {
                observer.observe(counter);
            });
        });

        // Fonction pour partager le site web
        function shareWebsite() {
            if (navigator.share) {
                navigator.share({
                    title: 'Répertoire des Patronymes du Burkina Faso',
                    text: 'Découvrez l\'origine et la signification des patronymes du Burkina Faso',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // Fallback pour les navigateurs qui ne supportent pas l'API Web Share
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('Lien copié dans le presse-papiers !');
                }).catch(() => {
                    // Fallback pour les navigateurs plus anciens
                    const textArea = document.createElement('textarea');
                    textArea.value = url;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    alert('Lien copié dans le presse-papiers !');
                });
            }
        }

        // Real-time search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            let realTimeDebounceTimer = null;

            if (!searchInput) return;

            // Normalize search query for better matching
            function normalizeSearchQuery(query) {
                const accentMap = {
                    'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a',
                    'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
                    'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
                    'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o',
                    'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u',
                    'ý': 'y', 'ÿ': 'y',
                    'ñ': 'n',
                    'ç': 'c'
                };

                let normalized = query.toLowerCase();
                for (const [accented, normal] of Object.entries(accentMap)) {
                    normalized = normalized.replace(new RegExp(accented, 'g'), normal);
                }
                return normalized;
            }

            // Real-time search functions
            function showRealTimeLoading() {
                const realTimeContainer = document.getElementById('welcome-real-time-results');
                if (realTimeContainer) {
                    realTimeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i> Recherche en cours...</div>';
                    realTimeContainer.classList.remove('hidden');
                }
            }

            function hideRealTimeResults() {
                const realTimeContainer = document.getElementById('welcome-real-time-results');
                if (realTimeContainer) {
                    realTimeContainer.classList.add('hidden');
                }
            }

            function displayRealTimeResults(data) {
                const realTimeContainer = document.getElementById('welcome-real-time-results');
                if (!realTimeContainer) {
                    console.error('Real-time results container not found');
                    return;
                }

                if (!data.patronymes || data.patronymes.length === 0) {
                    realTimeContainer.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-search text-4xl mb-4"></i><p>Aucun patronyme trouvé pour cette recherche.</p></div>';
                    realTimeContainer.classList.remove('hidden');
                    return;
                }

                // Create HTML for real-time results
                const html = `
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                            Résultats en temps réel (${data.patronymes.length} trouvé${data.patronymes.length > 1 ? 's' : ''})
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${data.patronymes.map(patronyme => `
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-semibold text-lg text-gray-900">${patronyme.nom}</h4>
                                    </div>
                                    ${patronyme.signification ? `<p class="text-gray-600 text-sm mb-2">${patronyme.signification}</p>` : ''}
                                    ${patronyme.region ? `<p class="text-blue-600 text-xs"><i class="fas fa-map-marker-alt mr-1"></i>${patronyme.region.nom}</p>` : ''}
                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-eye mr-1"></i>${patronyme.views_count || 0} vues
                                        </span>
                                        <a href="/patronymes/${patronyme.id}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Voir détails <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        <div class="mt-4 text-center">
                            <button onclick="hideWelcomeRealTimeResults()"
                                    class="text-gray-500 hover:text-gray-700 text-sm">
                                <i class="fas fa-times mr-1"></i>Masquer les résultats en temps réel
                            </button>
                        </div>
                    </div>
                `;

                realTimeContainer.innerHTML = html;
                realTimeContainer.classList.remove('hidden');
            }

            function performRealTimeSearch(query) {
                console.log('Performing real-time search for:', query);

                if (query.length < 2) {
                    hideRealTimeResults();
                    return;
                }

                showRealTimeLoading();

                // Debounce the request
                clearTimeout(realTimeDebounceTimer);
                realTimeDebounceTimer = setTimeout(() => {
                    const normalizedQuery = normalizeSearchQuery(query);

                    fetch('/patronymes?search=' + encodeURIComponent(normalizedQuery) + '&ajax=1', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            displayRealTimeResults(data);
                        })
                        .catch(error => {
                            console.error('Error fetching real-time results:', error);
                            hideRealTimeResults();
                        });
                }, 500);
            }

            // Add event listener for real-time search
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                performRealTimeSearch(query);
            });

            // Global function to hide real-time results
            window.hideWelcomeRealTimeResults = hideRealTimeResults;
        });
    </script>
</body>
</html>
