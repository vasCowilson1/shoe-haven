@extends('layouts.public')
@section('title', 'Privacy Policy — ' . config('app.name', 'Shoe Haven'))
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 56px; height: 56px; border-radius: 14px; background: #fff3e6;">
                    <i class="bi bi-shield-check" style="font-size: 1.5rem; color: var(--sh-orange);"></i>
                </div>
                <div>
                    <h1 class="fw-bold mb-0">Privacy Policy</h1>
                    <small class="text-muted">Last updated: July 2026</small>
                </div>
            </div>
            <p class="text-muted mb-4">Your privacy matters to us. This policy explains how Shoe Haven collects, uses, and protects your personal information when you use our website and services.</p>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-1-circle me-2" style="color: var(--sh-orange);"></i>Information We Collect</h5>
                    <p class="text-muted mb-0">We collect your name, email address, phone number, delivery address, and payment details when you place an order or create an account.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-2-circle me-2" style="color: var(--sh-orange);"></i>How We Use Your Information</h5>
                    <p class="text-muted mb-0">We use your data to process orders, communicate order updates, improve our services, and send promotional offers (with your consent).</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-3-circle me-2" style="color: var(--sh-orange);"></i>Data Protection</h5>
                    <p class="text-muted mb-0">We implement security measures to protect your personal data. Your payment information is processed securely and not stored on our servers.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-4-circle me-2" style="color: var(--sh-orange);"></i>Third-Party Sharing</h5>
                    <p class="text-muted mb-0">We do not sell your data. We may share necessary information with delivery partners and payment processors to fulfill your order.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-5-circle me-2" style="color: var(--sh-orange);"></i>Contact</h5>
                    <p class="text-muted mb-0">For privacy concerns, contact us at <a href="mailto:support@shoehaven.com">support@shoehaven.com</a> or call +256 700 000 000.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection