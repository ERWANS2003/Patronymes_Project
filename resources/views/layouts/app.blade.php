<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Répertoire complet des patronymes du Burkina Faso - Découvrez l'origine, la signification et l'histoire des noms de famille burkinabés">
    <meta name="keywords" content="patronymes, Burkina Faso, noms de famille, origine, signification, histoire, culture">
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

    <title>@yield('title', config('app.name', 'Répertoire des Patronymes'))</title>

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Alpine.js for reactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Additional styles -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="h-full bg-gray-50" x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-white text-sm"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Patronymes BF</span>
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('patronymes.index') }}" class="nav-link {{ request()->routeIs('patronymes.*') ? 'nav-link-active' : '' }}">
                            <i class="fas fa-search mr-1"></i>Explorer
                        </a>
                        <a href="{{ route('patronymes.index') }}?featured=1" class="nav-link">
                            <i class="fas fa-star mr-1"></i>Populaires
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                            @if(Auth::user()->canContribute())
                                <a href="{{ route('patronymes.create') }}" class="nav-link {{ request()->routeIs('patronymes.create') ? 'nav-link-active' : '' }}">
                                    <i class="fas fa-plus mr-1"></i>Ajouter
                                </a>
                            @endif
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'nav-link-active' : '' }}">
                                    <i class="fas fa-cog mr-1"></i>Admin
                                </a>
                            @endif
                        @endauth
                    </div>

                    <!-- User Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            <div class="dropdown" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>

                                <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu">
                                    <a href="{{ route('dashboard') }}" class="dropdown-item">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                    </a>
                                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                                        <i class="fas fa-user mr-2"></i>Profil
                                    </a>
                                    <a href="{{ route('patronymes.index') }}?favorites=1" class="dropdown-item">
                                        <i class="fas fa-heart mr-2"></i>Mes favoris
                                    </a>
                                    @if(Auth::user()->canContribute())
                                        <a href="{{ route('contributions.index') }}" class="dropdown-item">
                                            <i class="fas fa-edit mr-2"></i>Mes contributions
                                        </a>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <div class="border-t border-gray-200 my-1"></div>
                                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                            <i class="fas fa-cog mr-2"></i>Administration
                                        </a>
                                        <a href="{{ route('admin.statistics') }}" class="dropdown-item">
                                            <i class="fas fa-chart-bar mr-2"></i>Statistiques
                                        </a>
                                    @endif
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item w-full text-left">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="nav-link">Connexion</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus mr-1"></i>S'inscrire
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
                    <a href="{{ route('patronymes.index') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-search mr-2"></i>Explorer
                    </a>
                    <a href="{{ route('patronymes.index') }}?featured=1" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-star mr-2"></i>Populaires
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        @if(Auth::user()->canContribute())
                            <a href="{{ route('patronymes.create') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                            </a>
                        @endif
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-cog mr-2"></i>Administration
                            </a>
                        @endif
                        <a href="{{ route('profile.show') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-user mr-2"></i>Profil
                        </a>
                        <a href="{{ route('patronymes.index') }}?favorites=1" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-heart mr-2"></i>Mes favoris
                        </a>
                        @if(Auth::user()->canContribute())
                            <a href="{{ route('contributions.index') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-edit mr-2"></i>Mes contributions
                            </a>
                        @endif
                        <div class="border-t border-gray-200 my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                        </a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-user-plus mr-2"></i>S'inscrire
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

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
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
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
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-yellow-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                {{ session('warning') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                {{ session('info') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
</body>
</html>
