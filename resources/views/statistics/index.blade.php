<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistiques du Répertoire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Total Patronymes</h5>
                                    <h2 class="mb-0">{{ number_format($stats['total_patronymes']) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Régions</h5>
                                    <h2 class="mb-0">{{ number_format($stats['total_regions']) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Utilisateurs</h5>
                                    <h2 class="mb-0">{{ number_format($stats['total_users']) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-heart fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title">Favoris</h5>
                                    <h2 class="mb-0">{{ number_format($stats['total_favorites']) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Most Viewed Patronymes -->
                <div class="col-md-6 mb-4">
                    <div class="card animate__animated animate__fadeInLeft">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-eye text-primary"></i> Patronymes les plus consultés
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($stats['most_viewed']->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($stats['most_viewed'] as $patronyme)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $patronyme->nom }}</strong>
                                                @if($patronyme->region)
                                                    <br><small class="text-muted">{{ $patronyme->region->name }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ number_format($patronyme->views_count) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Patronymes -->
                <div class="col-md-6 mb-4">
                    <div class="card animate__animated animate__fadeInRight">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock text-success"></i> Patronymes récents
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($stats['recent_patronymes']->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($stats['recent_patronymes'] as $patronyme)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $patronyme->nom }}</strong>
                                                    @if($patronyme->region)
                                                        <br><small class="text-muted">{{ $patronyme->region->name }}</small>
                                                    @endif
                                                </div>
                                                <small class="text-muted">{{ $patronyme->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Patronymes by Region -->
                <div class="col-md-6 mb-4">
                    <div class="card animate__animated animate__fadeInUp">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-map text-info"></i> Répartition par région
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($stats['patronymes_by_region']->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($stats['patronymes_by_region'] as $region)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $region->name }}</span>
                                            <span class="badge bg-info rounded-pill">{{ $region->count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Patronymes by Ethnic Group -->
                <div class="col-md-6 mb-4">
                    <div class="card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users text-warning"></i> Répartition par groupe ethnique
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($stats['patronymes_by_ethnic_group']->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($stats['patronymes_by_ethnic_group'] as $group)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $group->nom }}</span>
                                            <span class="badge bg-warning rounded-pill">{{ $group->count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
