<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('home') }}</loc><priority>1.0</priority></url>
    <url><loc>{{ route('shop.index') }}</loc><priority>0.9</priority></url>
    <url><loc>{{ route('about.index') }}</loc><priority>0.6</priority></url>
    <url><loc>{{ route('contact.index') }}</loc><priority>0.5</priority></url>
    <url><loc>{{ route('help.index') }}</loc><priority>0.5</priority></url>
    <url><loc>{{ route('privacy.index') }}</loc><priority>0.3</priority></url>
    <url><loc>{{ route('terms.index') }}</loc><priority>0.3</priority></url>
    @foreach ($categories as $cat)
        <url><loc>{{ route('shop.index', ['category' => $cat->slug]) }}</loc><priority>0.7</priority></url>
    @endforeach
    @foreach ($products as $product)
        <url><loc>{{ route('shop.show', $product) }}</loc><priority>0.6</priority></url>
    @endforeach
</urlset>
