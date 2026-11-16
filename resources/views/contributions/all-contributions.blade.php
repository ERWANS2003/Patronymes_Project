<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gradient-bf">
                <i class="mr-2 fas fa-users-cog"></i>{{ __('Toutes les Contributions') }}
            </h2>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-bfNavy-600">
                    {{ $contributions->total() }} contribution(s) au total
                </span>
                <a href="{{ route('contributions.my') }}" class="btn btn-secondary text-sm">
                    <i class="fas fa-user mr-1"></i>Mes contributions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($contributions->count() > 0)
                <div class="grid gap-6">
                    @foreach($contributions as $contribution)
                        <div class="bg-white overflow-hidden shadow-glow-bf sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <h3 class="text-lg font-semibold text-bfNavy-800">
                                                <a href="{{ route('patronymes.show', $contribution->patronyme) }}"
                                                   class="hover:text-bfGreen-600 transition-colors">
                                                    {{ $contribution->patronyme->nom }}
                                                </a>
                                            </h3>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($contribution->statut === 'approuve') bg-bfGreen-100 text-bfGreen-800
                                                @elseif($contribution->statut === 'en_attente') bg-bfGold-100 text-bfGold-800
                                                @else bg-bfRed-100 text-bfRed-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $contribution->statut)) }}
                                            </span>
                                        </div>

                                        <p class="text-bfNavy-600 mb-3">{{ $contribution->contenu }}</p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <h4 class="font-semibold text-bfNavy-800 mb-1">Informations du patronyme :</h4>
                                                <div class="space-y-1 text-bfNavy-600">
                                                    <div>
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ $contribution->date_contribution->format('d/m/Y à H:i') }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                                        {{ $contribution->patronyme->region->nom ?? 'Non renseigné' }}
                                                    </div>
                                                    @if($contribution->patronyme->groupeEthnique)
                                                        <div>
                                                            <i class="fas fa-users mr-1"></i>
                                                            {{ $contribution->patronyme->groupeEthnique->nom }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div>
                                                <h4 class="font-semibold text-bfNavy-800 mb-1">Contributeur :</h4>
                                                <div class="space-y-1 text-bfNavy-600">
                                                    <div>
                                                        <i class="fas fa-user mr-1"></i>
                                                        {{ $contribution->contributeur->nom }}
                                                    </div>
                                                    @if($contribution->contributeur->user)
                                                        <div>
                                                            <i class="fas fa-envelope mr-1"></i>
                                                            {{ $contribution->contributeur->user->email }}
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-user-tag mr-1"></i>
                                                            <span class="px-2 py-1 text-xs rounded-full
                                                                @if($contribution->contributeur->user->isAdmin()) bg-bfRed-100 text-bfRed-800
                                                                @elseif($contribution->contributeur->user->isContributeur()) bg-bfGreen-100 text-bfGreen-800
                                                                @else bg-bfNavy-100 text-bfNavy-800 @endif">
                                                                {{ ucfirst($contribution->contributeur->user->role) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 ml-4">
                                        <a href="{{ route('patronymes.show', $contribution->patronyme) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye mr-1"></i>Voir
                                        </a>
                                        <a href="{{ route('contributions.show', $contribution) }}"
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-info-circle mr-1"></i>Détails
                                        </a>
                                        <form action="{{ route('contributions.destroy', $contribution) }}"
                                              method="POST" class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette contribution ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $contributions->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-bfGreen-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-users-cog text-3xl text-bfGreen-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-bfNavy-800 mb-2">Aucune contribution</h3>
                    <p class="text-bfNavy-600">Aucune contribution n'a encore été enregistrée dans le système.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
