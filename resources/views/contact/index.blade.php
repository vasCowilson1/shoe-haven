@extends('layouts.public')
@section('title', 'Contact Us — ' . config('app.name', 'Shoe Haven'))
@section('content')
<div class="container py-5">
    <div class="row g-5 justify-content-center">
        <div class="col-lg-6">
            <h1 class="fw-bold mb-2">Contact Us</h1>
            <p class="text-muted mb-4">Have a question? We'd love to hear from you. Send us a message and we'll respond promptly.</p>
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="btn btn-sh-orange">Send Message</button>
            </form>
        </div>
        <div class="col-lg-4">
            <div class="bg-dark text-white p-4 rounded-4">
                <h5 class="fw-bold mb-3">Get In Touch</h5>
                <div class="mb-3"><i class="bi bi-geo-alt me-2"></i> Kampala, Uganda</div>
                <div class="mb-3"><i class="bi bi-envelope me-2"></i> support@shoehaven.com</div>
                <div class="mb-3"><i class="bi bi-telephone me-2"></i> +256 700 000 000</div>
                <hr class="text-secondary">
                <p class="small text-white-50 mb-0">We typically respond within 24 hours during business days.</p>
            </div>
        </div>
    </div>
</div>
@endsection
