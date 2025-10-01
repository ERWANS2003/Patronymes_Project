<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-edit text-green-600 mr-2"></i>
                    Mes Contributions
                </h1>
                <p class="text-gray-600 mt-1">
                    Les patronymes que vous avez contribués à la plateforme
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                @if(Auth::user()->canContribute())
                    <a href="{{ route('patronymes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus mr-2"></i>Ajouter un patronyme
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if($contributions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                <i class="fas fa-list mr-2"></i>Vos contributions
                            </h3>
                            <p class="text-sm text-gray-600">
                                Voici les patronymes que vous avez contribués à la plateforme.
                            </p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Origine
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Région
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Groupe ethnique
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date d'ajout
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($contributions as $patronyme)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('patronymes.show', $patronyme) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                    {{ $patronyme->nom }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->origine ?? 'Non spécifiée' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->region->name ?? 'Non spécifiée' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $patronyme->groupeEthnique->nom ?? 'Non spécifié' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $patronyme->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                                <a href="{{ route('patronymes.show', $patronyme) }}" class="mr-3 text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                @if(Auth::user()->canContribute())
                                                    <a href="{{ route('patronymes.edit', $patronyme) }}" class="mr-3 text-yellow-600 hover:text-yellow-900">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $contributions->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-edit text-6xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Aucune contribution</h3>
                    <p class="mt-2 text-gray-500">
                        Vous n'avez pas encore contribué à la plateforme.
                    </p>
                    @if(Auth::user()->canContribute())
                        <div class="mt-6">
                            <a href="{{ route('patronymes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i> Ajouter votre premier patronyme
                            </a>
                        </div>
                    @else
                        <div class="mt-6">
                            <p class="text-sm text-gray-500">
                                Contactez un administrateur pour obtenir les permissions de contribution.
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
