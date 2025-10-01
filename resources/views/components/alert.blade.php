@props([
    'type' => 'info',
    'dismissible' => true,
    'icon' => true
])

@php
$typeClasses = [
    'primary' => 'alert-primary',
    'secondary' => 'alert-secondary',
    'success' => 'alert-success',
    'danger' => 'alert-danger',
    'warning' => 'alert-warning',
    'info' => 'alert-info',
    'light' => 'alert-light',
    'dark' => 'alert-dark'
];

$icons = [
    'primary' => 'fas fa-info-circle',
    'secondary' => 'fas fa-info-circle',
    'success' => 'fas fa-check-circle',
    'danger' => 'fas fa-exclamation-triangle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle',
    'light' => 'fas fa-info-circle',
    'dark' => 'fas fa-info-circle'
];
@endphp

<div class="alert {{ $typeClasses[$type] ?? 'alert-info' }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    @if($icon)
        <i class="{{ $icons[$type] ?? 'fas fa-info-circle' }} me-2"></i>
    @endif

    {{ $slot }}

    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
