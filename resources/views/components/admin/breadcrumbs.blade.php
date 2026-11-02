@props(['items' => []])

<nav class="sh-breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <li><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house-door"></i><span class="visually-hidden">Dashboard</span></a></li>
        @foreach ($items as $item)
            <li class="sh-breadcrumbs-sep" aria-hidden="true">/</li>
            @if (isset($item['url']))
                <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @else
                <li aria-current="page">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
