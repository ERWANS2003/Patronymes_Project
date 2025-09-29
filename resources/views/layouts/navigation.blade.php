<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <i class="fas fa-users text-indigo-600 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-gray-900">Répertoire des Patronymes</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-home mr-2"></i>Accueil
                    </a>
                    <a href="{{ route('patronymes.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-list mr-2"></i>Patronymes
                    </a>
                    <a href="{{ route('statistics.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                    @auth
                        <a href="{{ route('favorites.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                            <i class="fas fa-heart mr-2"></i>Mes Favoris
                        </a>

                        @if(Auth::user()->canContribute())
                            <a href="{{ route('patronymes.create') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-green-600 hover:text-green-700 hover:border-green-300 focus:outline-none focus:text-green-700 focus:border-green-300 transition duration-150 ease-in-out">
                                <i class="fas fa-plus mr-2"></i>Ajouter
                            </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-red-600 hover:text-red-700 hover:border-red-300 focus:outline-none focus:text-red-700 focus:border-red-300 transition duration-150 ease-in-out">
                                <i class="fas fa-cog mr-2"></i>Administration
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                @auth
                    <!-- User dropdown -->
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                            </button>
                        </div>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <div class="py-1">
                                <!-- User info -->
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if(Auth::user()->isAdmin())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-crown mr-1"></i>Administrateur
                                            </span>
                                        @elseif(Auth::user()->isContributeur())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-edit mr-1"></i>Contributeur
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-user mr-1"></i>Utilisateur
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Menu items -->
                                <a href="{{ route('profile.info') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Mon Profil
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Paramètres
                                </a>

                        @if(Auth::user()->canContribute())
                            <a href="{{ route('patronymes.create') }}" class="block px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                            </a>
                            <a href="{{ route('contributions.index') }}" class="block px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                <i class="fas fa-edit mr-2"></i>Mes contributions
                            </a>
                        @endif

                                @if(Auth::user()->isAdmin())
                                    <div class="border-t border-gray-100"></div>
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                                    </a>
                                    <a href="{{ route('admin.roles') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-users-cog mr-2"></i>Gestion des rôles
                                    </a>
                                    <a href="{{ route('admin.import') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-upload mr-2"></i>Import/Export
                                    </a>
                                @endif

                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-user-plus mr-2"></i>Inscription
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                <i class="fas fa-home mr-2"></i>Accueil
            </a>
            <a href="{{ route('patronymes.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                <i class="fas fa-list mr-2"></i>Patronymes
            </a>
            <a href="{{ route('statistics.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                <i class="fas fa-chart-bar mr-2"></i>Statistiques
            </a>
            @auth
                <a href="{{ route('favorites.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-heart mr-2"></i>Mes Favoris
                </a>
                <a href="{{ route('profile.info') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-user mr-2"></i>Mon Profil
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-tools mr-2"></i>Administration
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                </a>
                <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-user-plus mr-2"></i>Inscription
                </a>
            @endauth
        </div>
    </div>
</nav>
