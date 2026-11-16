<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gradient-bf">
                <i class="mr-2 fas fa-info-circle"></i>{{ __('Détails de la Contribution') }}
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('contributions.my') }}" class="btn btn-secondary text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Retour
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('contributions.all') }}" class="btn btn-secondary text-sm">
                        <i class="fas fa-list mr-1"></i>Toutes les contributions
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-glow-bf sm:rounded-lg">
                <div class="p-6">
                    <!-- En-tête de la contribution -->
                    <div class="border-b border-bfNavy-200 pb-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-2xl font-bold text-bfNavy-800">
                                <a href="{{ route('patronymes.show', $contribution->patronyme) }}"
                                   class="hover:text-bfGreen-600 transition-colors">
                                    {{ $contribution->patronyme->nom }}
                                </a>
                            </h3>
                            <span class="px-3 py-1 text-sm font-medium rounded-full
                                @if($contribution->statut === 'approuve') bg-bfGreen-100 text-bfGreen-800
                                @elseif($contribution->statut === 'en_attente') bg-bfGold-100 text-bfGold-800
                                @else bg-bfRed-100 text-bfRed-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $contribution->statut)) }}
                            </span>
                        </div>

                        <p class="text-bfNavy-600 text-lg">{{ $contribution->contenu }}</p>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Informations du patronyme -->
                        <div>
                            <h4 class="text-lg font-semibold text-bfNavy-800 mb-4 flex items-center">
                                <i class="fas fa-book mr-2 text-bfGreen-600"></i>
                                Informations du Patronyme
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-bfNavy-500 w-5"></i>
                                    <span class="ml-3 text-bfNavy-700">
                                        <strong>Date de contribution :</strong>
                                        {{ $contribution->date_contribution->format('d/m/Y à H:i') }}
                                    </span>
                                </div>

                                @if($contribution->patronyme->region)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Région :</strong> {{ $contribution->patronyme->region->nom }}
                                        </span>
                                    </div>
                                @endif

                                @if($contribution->patronyme->province)
                                    <div class="flex items-center">
                                        <i class="fas fa-building text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Province :</strong> {{ $contribution->patronyme->province->nom }}
                                        </span>
                                    </div>
                                @endif

                                @if($contribution->patronyme->commune)
                                    <div class="flex items-center">
                                        <i class="fas fa-home text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Commune :</strong> {{ $contribution->patronyme->commune->nom }}
                                        </span>
                                    </div>
                                @endif

                                @if($contribution->patronyme->groupeEthnique)
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Groupe ethnique :</strong> {{ $contribution->patronyme->groupeEthnique->nom }}
                                        </span>
                                    </div>
                                @endif

                                @if($contribution->patronyme->langue)
                                    <div class="flex items-center">
                                        <i class="fas fa-language text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Langue :</strong> {{ $contribution->patronyme->langue->nom }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Informations du contributeur -->
                        <div>
                            <h4 class="text-lg font-semibold text-bfNavy-800 mb-4 flex items-center">
                                <i class="fas fa-user-edit mr-2 text-bfGold-600"></i>
                                Informations du Contributeur
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-bfNavy-500 w-5"></i>
                                    <span class="ml-3 text-bfNavy-700">
                                        <strong>Nom :</strong> {{ $contribution->contributeur->nom }}
                                    </span>
                                </div>

                                @if($contribution->contributeur->user)
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Email :</strong> {{ $contribution->contributeur->user->email }}
                                        </span>
                                    </div>

                                    <div class="flex items-center">
                                        <i class="fas fa-user-tag text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Rôle :</strong>
                                            <span class="px-2 py-1 text-xs rounded-full ml-2
                                                @if($contribution->contributeur->user->isAdmin()) bg-bfRed-100 text-bfRed-800
                                                @elseif($contribution->contributeur->user->isContributeur()) bg-bfGreen-100 text-bfGreen-800
                                                @else bg-bfNavy-100 text-bfNavy-800 @endif">
                                                {{ ucfirst($contribution->contributeur->user->role) }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-plus text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Membre depuis :</strong>
                                            {{ $contribution->contributeur->user->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                @endif

                                @if($contribution->contributeur->contact)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-bfNavy-500 w-5"></i>
                                        <span class="ml-3 text-bfNavy-700">
                                            <strong>Contact :</strong> {{ $contribution->contributeur->contact }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-bfNavy-200 flex justify-between">
                        <a href="{{ route('patronymes.show', $contribution->patronyme) }}"
                           class="btn btn-primary">
                            <i class="fas fa-eye mr-2"></i>Voir le patronyme
                        </a>

                        <div class="flex space-x-3">
                            <a href="{{ route('contributions.my') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Retour aux contributions
                            </a>

                            <form action="{{ route('contributions.destroy', $contribution) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette contribution ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
