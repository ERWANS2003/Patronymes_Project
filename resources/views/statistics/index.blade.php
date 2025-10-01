<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                    Statistiques du Répertoire
                </h1>
                <p class="text-gray-600 mt-1">
                    Vue d'ensemble des données et performances du système
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('patronymes.index') }}" class="btn btn-outline">
                    <i class="fas fa-list mr-2"></i>Voir les patronymes
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Patronymes -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Patronymes</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_patronymes'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-blue-100 text-sm">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>+12% ce mois</span>
                </div>
            </div>

            <!-- Régions -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Régions couvertes</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_regions'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-green-100 text-sm">
                    <i class="fas fa-check mr-1"></i>
                    <span>Toutes les régions</span>
                </div>
            </div>

            <!-- Utilisateurs -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Utilisateurs actifs</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_users'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-purple-100 text-sm">
                    <i class="fas fa-user-plus mr-1"></i>
                    <span>+5 nouveaux cette semaine</span>
                </div>
            </div>

            <!-- Favoris -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">Favoris totaux</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['total_favorites'] ?? 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-red-100 text-sm">
                    <i class="fas fa-heart mr-1"></i>
                    <span>Engagement élevé</span>
                </div>
            </div>
        </div>

        <!-- Grille de contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Patronymes les plus consultés -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-eye text-blue-600 mr-2"></i>
                        Patronymes les plus consultés
                    </h3>
                    <span class="text-sm text-gray-500">Top 10</span>
                </div>

                @if(isset($stats['most_viewed']) && $stats['most_viewed']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['most_viewed'] as $index => $patronyme)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-blue-600">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h4>
                                    @if($patronyme->region)
                                        <p class="text-sm text-gray-600">{{ $patronyme->region->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-eye mr-1"></i>{{ number_format($patronyme->views_count) }}
                                    </span>
                                    <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary text-sm px-3 py-1">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-eye text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Patronymes récents -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        Patronymes récents
                    </h3>
                    <a href="{{ route('patronymes.index') }}" class="btn btn-outline text-sm">
                        Voir tout
                    </a>
                </div>

                @if(isset($stats['recent_patronymes']) && $stats['recent_patronymes']->count() > 0)
                    <div class="space-y-4">
                        @foreach($stats['recent_patronymes'] as $patronyme)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-book text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $patronyme->nom }}</h4>
                                    @if($patronyme->region)
                                        <p class="text-sm text-gray-600">{{ $patronyme->region->name }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">{{ $patronyme->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary text-sm px-3 py-1">
                                        Voir
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Répartition par région -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-map text-purple-600 mr-2"></i>
                        Répartition par région
                    </h3>
                    <span class="text-sm text-gray-500">{{ $stats['patronymes_by_region']->count() ?? 0 }} régions</span>
                </div>

                @if(isset($stats['patronymes_by_region']) && $stats['patronymes_by_region']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['patronymes_by_region'] as $region)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-purple-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $region->name }}</span>
                                </div>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $region->count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-map text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>

            <!-- Répartition par groupe ethnique -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-users text-orange-600 mr-2"></i>
                        Répartition par groupe ethnique
                    </h3>
                    <span class="text-sm text-gray-500">{{ $stats['patronymes_by_ethnic_group']->count() ?? 0 }} groupes</span>
                </div>

                @if(isset($stats['patronymes_by_ethnic_group']) && $stats['patronymes_by_ethnic_group']->count() > 0)
                    <div class="space-y-3">
                        @foreach($stats['patronymes_by_ethnic_group'] as $group)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-users text-orange-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $group->nom }}</span>
                                </div>
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $group->count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Actions rapides
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('patronymes.index') }}" class="btn btn-primary w-full justify-center">
                    <i class="fas fa-search mr-2"></i>Explorer les patronymes
                </a>
                <a href="{{ route('patronymes.create') }}" class="btn btn-success w-full justify-center">
                    <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                </a>
                <a href="{{ route('favorites.index') }}" class="btn btn-secondary w-full justify-center">
                    <i class="fas fa-heart mr-2"></i>Mes favoris
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline w-full justify-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                </a>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
