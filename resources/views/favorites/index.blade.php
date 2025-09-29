<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Favoris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($favorites->count() > 0)
                <div class="row">
                    @foreach($favorites as $patronyme)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 animate__animated animate__fadeInUp">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title text-primary">{{ $patronyme->nom }}</h5>
                                        <button class="btn btn-outline-danger btn-sm favorite-btn"
                                                data-patronyme-id="{{ $patronyme->id }}"
                                                data-favorited="true">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>

                                    @if($patronyme->signification)
                                        <p class="card-text">
                                            <strong>Signification:</strong> {{ Str::limit($patronyme->signification, 100) }}
                                        </p>
                                    @endif

                                    @if($patronyme->origine)
                                        <p class="card-text">
                                            <strong>Origine:</strong> {{ Str::limit($patronyme->origine, 100) }}
                                        </p>
                                    @endif

                                    <div class="row text-muted small">
                                        @if($patronyme->region)
                                            <div class="col-6">
                                                <i class="fas fa-map-marker-alt"></i> {{ $patronyme->region->name }}
                                            </div>
                                        @endif
                                        @if($patronyme->province)
                                            <div class="col-6">
                                                <i class="fas fa-building"></i> {{ $patronyme->province->nom }}
                                            </div>
                                        @endif
                                        @if($patronyme->commune)
                                            <div class="col-6">
                                                <i class="fas fa-home"></i> {{ $patronyme->commune->nom }}
                                            </div>
                                        @endif
                                        @if($patronyme->groupeEthnique)
                                            <div class="col-6">
                                                <i class="fas fa-users"></i> {{ $patronyme->groupeEthnique->nom }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <a href="{{ route('patronymes.show', $patronyme) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer text-muted">
                                    <small>
                                        <i class="fas fa-eye"></i> {{ number_format($patronyme->views_count ?? 0) }} vues
                                        <span class="ms-3">
                                            <i class="fas fa-heart"></i> {{ $patronyme->favorites()->count() }} favoris
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $favorites->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="animate__animated animate__fadeIn">
                        <i class="fas fa-heart-broken fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted">Aucun favori pour le moment</h3>
                        <p class="text-muted">Commencez à explorer les patronymes et ajoutez vos favoris !</p>
                        <a href="{{ route('patronymes.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Explorer les patronymes
                        </a>
                    </div>
                </div>
            @endif
        </div>
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
