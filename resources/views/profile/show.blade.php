<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Profil Utilisateur
                </h1>
                <p class="text-gray-600 mt-1">
                    Gérez vos informations personnelles et vos préférences
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                            Informations personnelles
                        </h3>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="form-label">Nom complet</label>
                                    <p class="text-gray-900 font-medium">{{ Auth::user()->name }}</p>
                                </div>
                                <div>
                                    <label class="form-label">Email</label>
                                    <p class="text-gray-900 font-medium">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="form-label">Membre depuis</label>
                                    <p class="text-gray-900 font-medium">{{ Auth::user()->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <label class="form-label">Dernière connexion</label>
                                    <p class="text-gray-900 font-medium">
                                        {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'Jamais' }}
                                    </p>
                                </div>
                            </div>
                        </div>
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
                            <a href="{{ route('dashboard') }}" class="btn btn-primary w-full justify-start">
                                <i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord
                            </a>
                            <a href="{{ route('patronymes.index') }}" class="btn btn-secondary w-full justify-start">
                                <i class="fas fa-search mr-2"></i>Explorer les patronymes
                            </a>
                            <a href="{{ route('patronymes.index') }}?favorites=1" class="btn btn-secondary w-full justify-start">
                                <i class="fas fa-heart mr-2"></i>Mes favoris
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                            Mes statistiques
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Favoris</span>
                                <span class="font-semibold text-gray-900">{{ Auth::user()->favorites()->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Contributions</span>
                                <span class="font-semibold text-gray-900">{{ Auth::user()->contributions()->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Recherches</span>
                                <span class="font-semibold text-gray-900">{{ Auth::user()->searchLogs()->count() ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
