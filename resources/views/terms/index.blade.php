@extends('layouts.public')
@section('title', 'Terms of Service — ' . config('app.name', 'Shoe Haven'))
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 56px; height: 56px; border-radius: 14px; background: #fff3e6;">
                    <i class="bi bi-file-earmark-text" style="font-size: 1.5rem; color: var(--sh-orange);"></i>
                </div>
                <div>
                    <h1 class="fw-bold mb-0">Terms of Service</h1>
                    <small class="text-muted">Last updated: July 2026</small>
                </div>
            </div>
            <p class="text-muted mb-4">By using Shoe Haven, you agree to these terms. Please read them carefully.</p>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-1-circle me-2" style="color: var(--sh-orange);"></i>Orders & Payment</h5>
                    <p class="text-muted mb-0">All prices are in Ugandan Shillings (UGX). Payment is due at checkout via MTN Mobile Money or Airtel Money. Orders are processed after payment confirmation.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-2-circle me-2" style="color: var(--sh-orange);"></i>Shipping & Delivery</h5>
                    <p class="text-muted mb-0">We deliver within 2–5 business days across Uganda. Free delivery applies to orders over UGX 200,000. Delivery times may vary based on location.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-3-circle me-2" style="color: var(--sh-orange);"></i>Returns & Refunds</h5>
                    <p class="text-muted mb-0">You may return unworn shoes within 14 days of delivery for a refund or exchange. Items must be in original condition with packaging.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-4-circle me-2" style="color: var(--sh-orange);"></i>Account Responsibility</h5>
                    <p class="text-muted mb-0">You are responsible for maintaining the confidentiality of your account credentials and for all activities under your account.</p>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-5-circle me-2" style="color: var(--sh-orange);"></i>Changes</h5>
                    <p class="text-muted mb-0">We reserve the right to update these terms at any time. Continued use of the site constitutes acceptance of changes.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection