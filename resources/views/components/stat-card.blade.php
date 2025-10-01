@props([
    'title',
    'value',
    'icon',
    'color' => 'primary',
    'trend' => null,
    'trendValue' => null,
    'description' => null
])

@php
$colorClasses = [
    'primary' => 'bg-primary',
    'secondary' => 'bg-secondary',
    'success' => 'bg-success',
    'danger' => 'bg-danger',
    'warning' => 'bg-warning',
    'info' => 'bg-info',
    'light' => 'bg-light text-dark',
    'dark' => 'bg-dark'
];

$trendClasses = [
    'up' => 'text-success',
    'down' => 'text-danger',
    'neutral' => 'text-muted'
];
@endphp

<div class="card h-100 shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="rounded-circle {{ $colorClasses[$color] ?? 'bg-primary' }} p-3 text-white">
                    <i class="{{ $icon }} fa-lg"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h6 class="card-title text-muted mb-1">{{ $title }}</h6>
                <h3 class="mb-0 fw-bold">{{ $value }}</h3>
                @if($description)
                    <small class="text-muted">{{ $description }}</small>
                @endif
            </div>
            @if($trend && $trendValue)
                <div class="flex-shrink-0 text-end">
                    <div class="d-flex align-items-center {{ $trendClasses[$trend] ?? 'text-muted' }}">
                        <i class="fas fa-arrow-{{ $trend === 'up' ? 'up' : ($trend === 'down' ? 'down' : 'right') }} me-1"></i>
                        <span class="fw-bold">{{ $trendValue }}</span>
                    </div>
                    <small class="text-muted">vs mois dernier</small>
                </div>
            @endif
        </div>
    </div>
</div>
