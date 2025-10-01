@props([
    'size' => 'md',
    'color' => 'primary',
    'text' => 'Chargement...'
])

@php
$sizeClasses = [
    'sm' => 'spinner-border-sm',
    'md' => '',
    'lg' => 'spinner-border-lg',
    'xl' => 'spinner-border-xl'
];

$colorClasses = [
    'primary' => 'text-primary',
    'secondary' => 'text-secondary',
    'success' => 'text-success',
    'danger' => 'text-danger',
    'warning' => 'text-warning',
    'info' => 'text-info',
    'light' => 'text-light',
    'dark' => 'text-dark'
];
@endphp

<div class="d-flex flex-column align-items-center justify-content-center p-4">
    <div class="spinner-border {{ $sizeClasses[$size] ?? '' }} {{ $colorClasses[$color] ?? 'text-primary' }}" role="status">
        <span class="visually-hidden">{{ $text }}</span>
    </div>
    @if($text)
        <div class="mt-2 text-muted">{{ $text }}</div>
    @endif
</div>
