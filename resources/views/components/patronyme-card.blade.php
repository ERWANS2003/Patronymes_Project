@props([
    'patronyme',
    'showActions' => true
])

<div class="card h-100 shadow-sm hover-shadow-lg transition-all">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="card-title text-primary mb-0">
                <i class="fas fa-user me-2"></i>{{ $patronyme->nom }}
            </h5>
            @if($showActions)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('patronymes.show', $patronyme) }}">
                                <i class="fas fa-eye me-2"></i>Voir
                            </a>
                        </li>
                        @auth
                            @if(Auth::user()->canContribute())
                                <li>
                                    <a class="dropdown-item" href="{{ route('patronymes.edit', $patronyme) }}">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a>
                                </li>
                            @endif
                            <li>
                                <button class="dropdown-item" onclick="toggleFavorite({{ $patronyme->id }})">
                                    <i class="fas fa-heart me-2" id="heart-{{ $patronyme->id }}"></i>
                                    <span id="favorite-text-{{ $patronyme->id }}">Ajouter aux favoris</span>
                                </button>
                            </li>
                        @endauth
                    </ul>
                </div>
            @endif
        </div>

        @if($patronyme->signification)
            <p class="card-text text-muted mb-2">
                <i class="fas fa-info-circle me-1"></i>
                {{ Str::limit($patronyme->signification, 100) }}
            </p>
        @endif

        @if($patronyme->origine)
            <p class="card-text text-muted mb-2">
                <i class="fas fa-map-marker-alt me-1"></i>
                {{ Str::limit($patronyme->origine, 80) }}
            </p>
        @endif

        <div class="row g-2 mb-3">
            @if($patronyme->region)
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-globe me-1"></i>{{ $patronyme->region->name }}
                    </small>
                </div>
            @endif
            @if($patronyme->groupeEthnique)
                <div class="col-6">
                    <small class="text-muted">
                        <i class="fas fa-users me-1"></i>{{ $patronyme->groupeEthnique->nom }}
                    </small>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    <i class="fas fa-eye me-1"></i>{{ $patronyme->views_count ?? 0 }}
                </span>
                <span class="badge bg-success">
                    <i class="fas fa-heart me-1"></i>{{ $patronyme->favorites()->count() }}
                </span>
                @if($patronyme->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star me-1"></i>Mis en avant
                    </span>
                @endif
            </div>
            <small class="text-muted">
                {{ $patronyme->created_at->diffForHumans() }}
            </small>
        </div>
    </div>
</div>

<style>
.hover-shadow-lg:hover {
    box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
}

.transition-all {
    transition: all 0.3s ease;
}
</style>
