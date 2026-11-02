@props([
    'type' => 'secondary',
    'icon' => null,
])

<span class="sh-badge sh-badge-{{ $type }}" {{ $attributes }}>
    @if ($icon)<i class="bi {{ $icon }} me-1"></i>@endif
    {{ $slot }}
</span>
