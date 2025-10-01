@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'autohide' => true,
    'delay' => 5000
])

@php
$typeClasses = [
    'success' => 'bg-success',
    'error' => 'bg-danger',
    'warning' => 'bg-warning',
    'info' => 'bg-info',
    'primary' => 'bg-primary'
];

$icons = [
    'success' => 'fas fa-check-circle',
    'error' => 'fas fa-exclamation-circle',
    'warning' => 'fas fa-exclamation-triangle',
    'info' => 'fas fa-info-circle',
    'primary' => 'fas fa-bell'
];
@endphp

<div class="toast align-items-center text-white {{ $typeClasses[$type] ?? 'bg-info' }} border-0"
     role="alert"
     aria-live="assertive"
     aria-atomic="true"
     data-bs-autohide="{{ $autohide ? 'true' : 'false' }}"
     data-bs-delay="{{ $delay }}">
    <div class="d-flex">
        <div class="toast-body">
            @if($title)
                <strong>{{ $title }}</strong><br>
            @endif
            {{ $message }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>
