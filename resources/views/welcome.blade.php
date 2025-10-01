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
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
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
                        <a href="{{ route('register') }}" class="btn btn-primary">
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
                    <a href="{{ route('patronymes.index') }}?featured=1" class="btn btn-secondary text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-blue-600">
                        <i class="fas fa-fire mr-2"></i>Patronymes populaires
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-blue-600">
                            <i class="fas fa-user-plus mr-2"></i>Rejoindre la communauté
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline text-lg px-8 py-4 border-white text-white hover:bg-white hover:text-blue-600">
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
                                        {{ $region->name }}
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
                        <button type="submit" class="btn btn-primary px-8 py-3">
                            <i class="fas fa-search mr-2"></i>Rechercher
                        </button>
                    </div>
                </form>
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
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Base de Données Complète</h3>
                    <p class="text-gray-600 mb-4">
                        Accédez à des milliers de patronymes avec leurs origines, significations et histoires détaillées.
                    </p>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-primary text-sm">
                        <i class="fas fa-search mr-2"></i>Explorer
                    </a>
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Recherche Avancée</h3>
                    <p class="text-gray-600 mb-4">
                        Filtrez par région, groupe ethnique, langue et bien d'autres critères pour des résultats précis.
                    </p>
                    <a href="{{ route('statistics.index') }}" class="btn btn-success text-sm">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Communauté Active</h3>
                    <p class="text-gray-600 mb-4">
                        Contribuez à l'enrichissement de la base de données et partagez vos connaissances.
                    </p>
                    @auth
                        <a href="{{ route('patronymes.create') }}" class="btn btn-purple text-sm">
                            <i class="fas fa-plus mr-2"></i>Contribuer
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-outline text-sm">
                            <i class="fas fa-user-plus mr-2"></i>S'inscrire
                        </a>
                    @endauth
                </div>

                <div class="feature-card animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-2xl text-yellow-600"></i>
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
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-red-600"></i>
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
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-2xl text-indigo-600"></i>
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
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Notre Impact
                </h2>
                <p class="text-xl text-blue-100">
                    Des chiffres qui témoignent de notre engagement pour la préservation du patrimoine
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="animate-fade-in-up" style="animation-delay: 0.1s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\Patronyme::count() }}">0</div>
                    <p class="text-blue-100">Patronymes enregistrés</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.2s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\User::count() }}">0</div>
                    <p class="text-blue-100">Utilisateurs actifs</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.3s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\Region::count() }}">0</div>
                    <p class="text-blue-100">Régions couvertes</p>
                </div>
                <div class="animate-fade-in-up" style="animation-delay: 0.4s">
                    <div class="stats-counter text-4xl md:text-5xl font-bold mb-2" data-count="{{ \App\Models\GroupeEthnique::count() }}">0</div>
                    <p class="text-blue-100">Groupes ethniques</p>
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
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
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
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

                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Aide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Support</a></li>
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
    </script>
</body>
</html>
