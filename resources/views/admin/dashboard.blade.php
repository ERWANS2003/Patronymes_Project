<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    Administration
                </h1>
                <p class="text-gray-600 mt-1">
                    Gestion de la plateforme et des utilisateurs
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">
                    Connecté en tant qu'administrateur
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Admin Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Utilisateurs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Patronymes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Patronyme::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-map text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Régions</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Region::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Favoris</p>
                            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Favorite::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- User Management -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Gestion des utilisateurs
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-full justify-start">
                            <i class="fas fa-list mr-2"></i>Liste des utilisateurs
                        </a>
                        <a href="{{ route('admin.roles') }}" class="btn btn-secondary w-full justify-start">
                            <i class="fas fa-user-shield mr-2"></i>Gestion des rôles
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Management -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-book text-green-600 mr-2"></i>
                        Gestion du contenu
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.import') }}" class="btn btn-primary w-full justify-start">
                            <i class="fas fa-upload mr-2"></i>Importer des données
                        </a>
                        <a href="{{ route('admin.export') }}" class="btn btn-secondary w-full justify-start">
                            <i class="fas fa-download mr-2"></i>Exporter des données
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                        Statistiques
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.statistics') }}" class="btn btn-primary w-full justify-start">
                            <i class="fas fa-chart-line mr-2"></i>Voir les statistiques
                        </a>
                        <a href="{{ route('admin.monitoring.dashboard') }}" class="btn btn-secondary w-full justify-start">
                            <i class="fas fa-monitor mr-2"></i>Monitoring
                        </a>
                    </div>
                </div>
            </div>

            <!-- System -->
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-cogs text-orange-600 mr-2"></i>
                        Système
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.health.check') }}" class="btn btn-primary w-full justify-start">
                            <i class="fas fa-heartbeat mr-2"></i>Santé du système
                        </a>
                        <a href="{{ route('admin.metrics') }}" class="btn btn-secondary w-full justify-start">
                            <i class="fas fa-tachometer-alt mr-2"></i>Métriques
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8">
            <div class="card">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Activité récente
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Nouvel utilisateur inscrit</p>
                                <p class="text-xs text-gray-500">{{ now()->subHours(2)->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-book text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Nouveau patronyme ajouté</p>
                                <p class="text-xs text-gray-500">{{ now()->subHours(4)->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
