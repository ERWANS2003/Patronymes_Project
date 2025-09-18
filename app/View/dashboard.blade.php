<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-tachometer-alt"></i>{{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Welcome Banner -->
            <div class="mb-8 overflow-hidden bg-indigo-600 rounded-lg shadow-sm">
                <div class="px-6 py-8">
                    <h1 class="text-2xl font-bold text-white">Bienvenue, {{ Auth::user()->name }}!</h1>
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
                        <form action="{{ route('patronymes.index') }}" method="GET" class="space-y-4">
                            <div>
                                <input type="text" name="search" placeholder="Rechercher un patronyme..."
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
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
                            <a href="{{ route('patronymes.create') }}" class="flex items-center justify-center block w-full px-4 py-2 text-center text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                <i class="mr-2 fas fa-plus"></i> Ajouter un patronyme
                            </a>
                            <a href="{{ route('patronymes.index') }}" class="flex items-center justify-center block w-full px-4 py-2 text-center text-white bg-green-600 rounded-md hover:bg-green-700">
                                <i class="mr-2 fas fa-list"></i> Voir tous les patronymes
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-center block w-full px-4 py-2 text-center text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                <i class="mr-2 fas fa-user"></i> Modifier mon profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Derniers patronymes -->
            <div class="mb-8 overflow-hidden bg-white rounded-lg shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900">Derniers patronymes ajoutés</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Origine</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentPatronymes as $patronyme)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('patronymes.show', $patronyme) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $patronyme->nom }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $patronyme->origine ?? 'Non spécifiée' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $patronyme->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
