@extends('layouts.public')
@section('title', 'Help Center — ' . config('app.name', 'Shoe Haven'))
@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
             style="width: 72px; height: 72px; border-radius: 50%; background: #fff3e6;">
            <i class="bi bi-question-circle" style="font-size: 2rem; color: var(--sh-orange);"></i>
        </div>
        <h1 class="fw-bold">Help Center</h1>
        <p class="text-muted mx-auto" style="max-width: 500px;">Find answers to common questions about ordering, payments, delivery, and returns.</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark border-0">
                <div class="card-body p-4">
                    <div class="accordion" id="helpAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h1">How long does delivery take?</button></h2>
                            <div id="h1" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Delivery takes 2–5 business days anywhere in Uganda.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h2">What payment methods do you accept?</button></h2>
                            <div id="h2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">We accept MTN Mobile Money and Airtel Money.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h3">Can I return or exchange shoes?</button></h2>
                            <div id="h3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Yes. Unworn shoes in original condition can be returned within 14 days for a refund or exchange.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h4">How can I track my order?</button></h2>
                            <div id="h4" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Log in to your account and visit My Orders to see your order status. You'll also get updates via email or phone.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h5">Do you offer free delivery?</button></h2>
                            <div id="h5" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Yes, delivery is free for orders over UGX 200,000. A flat fee of UGX 20,000 applies for smaller orders.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h6">How do I find my shoe size?</button></h2>
                            <div id="h6" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Check our Size Guide on any product page for EU/UK/US conversions based on foot length in cm.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h7">Are your shoes authentic?</button></h2>
                            <div id="h7" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">Absolutely. We source directly from trusted manufacturers and guarantee 100% authentic products.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#h8">Can I cancel my order?</button></h2>
                            <div id="h8" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                <div class="accordion-body text-white-50">You can cancel within 1 hour of placing the order. After that, contact our support team for assistance.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted">Still need help? <a href="{{ route('contact.index') }}" style="color: var(--sh-orange);">Contact our support team</a></p>
            </div>
        </div>
    </div>
</div>
@endsection