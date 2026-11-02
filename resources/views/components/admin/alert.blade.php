@props([
    'type' => 'success',
    'message' => '',
    'dismissible' => true,
])

@php
    $icons = [
        'success' => 'bi-check-circle',
        'info'    => 'bi-info-circle',
        'warning' => 'bi-exclamation-triangle',
        'danger'  => 'bi-x-octagon',
    ];
@endphp

<div class="sh-alert sh-alert-{{ $type }}" role="alert" {{ $attributes }}>
    <i class="bi {{ $icons[$type] ?? $icons['info'] }} sh-alert-icon"></i>
    <div class="sh-alert-body">{{ $message ?? $slot }}</div>
    @if ($dismissible)
        <button type="button" class="sh-alert-close" data-bs-dismiss="alert" aria-label="Dismiss">
            <i class="bi bi-x-lg"></i>
        </button>
    @endif
</div>
