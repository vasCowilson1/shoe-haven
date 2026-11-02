@extends('layouts.public')

@section('title', 'Page Not Found — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5" style="min-height: 70vh;">
    <div class="text-center py-5">
        <div class="d-inline-flex align-items-center justify-content-center mb-4"
             style="width: 100px; height: 100px; border-radius: 50%; background: #fff3e6;">
            <i class="bi bi-search" style="font-size: 2.8rem; color: var(--sh-orange);"></i>
        </div>
        <h1 class="display-1 fw-bold mb-2" style="color: var(--sh-orange);">404</h1>
        <h2 class="fw-bold mb-3">Page Not Found</h2>
        <p class="text-muted mb-4 mx-auto" style="max-width: 440px;">
            Sorry, we couldn't find the page you're looking for. It might have been moved or doesn't exist.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <button onclick="window.history.back()" class="btn btn-outline-dark btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Go Back
            </button>
            <a href="{{ route('home') }}" class="btn btn-sh-orange btn-lg">
                <i class="bi bi-house me-2"></i>Return Home
            </a>
        </div>
    </div>
</div>
@endsection