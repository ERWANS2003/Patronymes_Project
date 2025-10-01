<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
                    Tableau de bord
                </h1>
                <p class="text-gray-600 mt-1">
                    Bienvenue, {{ Auth::user()->name }} ! Voici un aperçu de votre activité.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">
                    Dernière connexion: {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'Jamais' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Patronymes -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Patronymes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_patronymes'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mes Favoris -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-2xl text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Mes Favoris</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_favorites'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recherches -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Recherches</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_searches'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contributions -->
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Contributions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['my_contributions'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Patronymes -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                Patronymes récents
                            </h2>
                            <a href="{{ route('patronymes.index') }}" class="btn btn-outline text-sm">
                                Voir tout
                            </a>
                        </div>

                        @if(isset($recentPatronymes) && $recentPatronymes->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentPatronymes as $patronyme)
                                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-book text-blue-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h3>
                                            <p class="text-sm text-gray-600">
                                                @if($patronyme->groupeEthnique)
                                                    {{ $patronyme->groupeEthnique->nom }}
                                                @endif
                                                @if($patronyme->full_location)
                                                    • {{ $patronyme->full_location }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-eye mr-1"></i>{{ $patronyme->views_count }}
                                            </span>
                                            <a href="{{ route('patronymes.show', $patronyme) }}"
                                               class="btn btn-primary text-sm px-3 py-1">
                                                Voir
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">Aucun patronyme récent</p>
                                <a href="{{ route('patronymes.index') }}" class="btn btn-primary mt-4">
                                    Explorer les patronymes
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                            Actions rapides
                        </h3>
                        <div class="space-y-3">
                            <a href="{{ route('patronymes.index') }}" class="btn btn-primary w-full justify-start">
                                <i class="fas fa-search mr-2"></i>Explorer les patronymes
                            </a>
                            <a href="{{ route('patronymes.index') }}?featured=1" class="btn btn-secondary w-full justify-start">
                                <i class="fas fa-star mr-2"></i>Patronymes populaires
                            </a>
                            @auth
                                <a href="{{ route('patronymes.index') }}?favorites=1" class="btn btn-secondary w-full justify-start">
                                    <i class="fas fa-heart mr-2"></i>Mes favoris
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Popular Patronymes -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-fire text-orange-500 mr-2"></i>
                            Patronymes populaires
                        </h3>

                        @if(isset($popularPatronymes) && $popularPatronymes->count() > 0)
                            <div class="space-y-3">
                                @foreach($popularPatronymes as $patronyme)
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-orange-600">{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $patronyme->nom }}</h4>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-eye mr-1"></i>{{ $patronyme->views_count }} vues
                                            </p>
                                        </div>
                                        <a href="{{ route('patronymes.show', $patronyme) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aucun patronyme populaire</p>
                        @endif
                    </div>
                </div>

                <!-- Statistics Summary -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                            Statistiques
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Régions couvertes</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_regions'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Groupes ethniques</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_groupes'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Langues</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_langues'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Utilisateurs actifs</span>
                                <span class="font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @auth
            <div class="mt-8">
                <div class="card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">
                            <i class="fas fa-history text-blue-600 mr-2"></i>
                            Activité récente
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">Vous vous êtes connecté</p>
                                    <p class="text-xs text-gray-500">{{ now()->diffForHumans() }}</p>
                                </div>
                            </div>

                            @if($stats['my_favorites'] > 0)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-heart text-red-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">Vous avez {{ $stats['my_favorites'] }} patronyme{{ $stats['my_favorites'] > 1 ? 's' : '' }} en favori</p>
                                        <p class="text-xs text-gray-500">Dernière mise à jour: {{ now()->subHours(2)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</x-app-layout>
