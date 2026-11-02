@props([
    'name' => '',
    'src' => null,
    'size' => 40,
])

@php
    $initials = collect(explode(' ', trim($name)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
    $palette = ['#e6532a', '#2a7de6', '#22a05a', '#b039c4', '#d99a1f', '#0e9aa7'];
    $color = $palette[crc32($name ?: '?') % count($palette)];
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" class="sh-avatar" style="width:{{ $size }}px;height:{{ $size }}px;">
@else
    <span class="sh-avatar" style="width:{{ $size }}px;height:{{ $size }}px;background:{{ $color }};" aria-hidden="true">{{ $initials ?: '?' }}</span>
@endif
