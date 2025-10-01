<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-heart text-red-600 mr-2"></i>
                    Mes Favoris
                </h1>
                <p class="text-gray-600 mt-1">
                    Vos patronymes préférés sauvegardés
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('patronymes.index') }}" class="btn btn-outline">
                    <i class="fas fa-search mr-2"></i>Explorer les patronymes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($favorites as $patronyme)
                    <div class="card card-hover">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $patronyme->nom }}</h3>
                                <button class="btn btn-sm btn-danger favorite-btn"
                                        data-patronyme-id="{{ $patronyme->id }}"
                                        data-favorited="true">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>

                            @if($patronyme->signification)
                                <p class="text-gray-600 mb-3">
                                    <strong>Signification:</strong> {{ Str::limit($patronyme->signification, 100) }}
                                </p>
                            @endif

                            @if($patronyme->origine)
                                <p class="text-gray-600 mb-4">
                                    <strong>Origine:</strong> {{ Str::limit($patronyme->origine, 100) }}
                                </p>
                            @endif

                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-500 mb-4">
                                @if($patronyme->region)
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        {{ $patronyme->region->name }}
                                    </div>
                                @endif
                                @if($patronyme->province)
                                    <div class="flex items-center">
                                        <i class="fas fa-building mr-2"></i>
                                        {{ $patronyme->province->nom }}
                                    </div>
                                @endif
                                @if($patronyme->commune)
                                    <div class="flex items-center">
                                        <i class="fas fa-home mr-2"></i>
                                        {{ $patronyme->commune->nom }}
                                    </div>
                                @endif
                                @if($patronyme->groupeEthnique)
                                    <div class="flex items-center">
                                        <i class="fas fa-users mr-2"></i>
                                        {{ $patronyme->groupeEthnique->nom }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-between items-center">
                                <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye mr-1"></i>Voir détails
                                </a>
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-eye mr-1"></i>{{ number_format($patronyme->views_count ?? 0) }} vues
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-heart-broken text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Aucun favori pour le moment</h3>
                <p class="text-gray-500 mb-6">Commencez à explorer les patronymes et ajoutez vos favoris !</p>
                <a href="{{ route('patronymes.index') }}" class="btn btn-primary">
                    <i class="fas fa-search mr-2"></i>Explorer les patronymes
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle favorite toggle
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const patronymeId = this.dataset.patronymeId;
                    const isFavorited = this.dataset.favorited === 'true';

                    fetch(`/patronymes/${patronymeId}/favorite`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.isFavorited) {
                            this.innerHTML = '<i class="fas fa-heart"></i>';
                            this.classList.remove('btn-outline-danger');
                            this.classList.add('btn-danger');
                            this.dataset.favorited = 'true';
                        } else {
                            this.innerHTML = '<i class="far fa-heart"></i>';
                            this.classList.remove('btn-danger');
                            this.classList.add('btn-outline-danger');
                            this.dataset.favorited = 'false';
                        }

                        // Update favorites count
                        const countElement = this.closest('.card').querySelector('.fa-heart').parentElement;
                        if (countElement) {
                            countElement.innerHTML = `<i class="fas fa-heart"></i> ${data.favoritesCount} favoris`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la mise à jour des favoris.');
                    });
                });
            });
        });
    </script>
</x-app-layout>
