@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'href' => null,
    'type' => 'button',
    'loading' => false,
])

@php
    $classes = 'sh-btn sh-btn-' . $variant . ' sh-btn-' . $size;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading)<span class="sh-spinner" aria-hidden="true"></span>@endif
        @if ($icon)<i class="bi {{ $icon }} me-2"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if ($loading) disabled @endif>
        @if ($loading)<span class="sh-spinner" aria-hidden="true"></span>@endif
        @if ($icon)<i class="bi {{ $icon }} me-2"></i>@endif
        {{ $slot }}
    </button>
@endif
