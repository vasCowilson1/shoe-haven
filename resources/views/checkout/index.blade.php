@extends('layouts.public')

@section('title', 'Checkout — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('cart.index') }}" class="btn btn-outline-light btn-sm rounded-circle" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">Checkout</h2>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card bg-dark border-0 p-4">
                <h5 class="fw-bold mb-4">Delivery Details</h5>
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $profile?->full_name ?? auth()->user()->name ?? '') }}" required autocomplete="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+256 700 000 000" value="{{ old('phone', $profile?->phone) }}" required autocomplete="tel">
                    </div>

                    <div class="border rounded-3 p-3 mb-3" style="border-color: rgba(255,255,255,.12)!important;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><i class="bi bi-geo-alt me-2"></i>Delivery Location</h6>
                                <small class="text-white-50">Select your area so we can route your delivery correctly.</small>
                            </div>
                            @if ($profile?->delivery_region)
                                <span class="badge bg-success-subtle text-success">Saved location</span>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="delivery-region" class="form-label">Region</label>
                                <select id="delivery-region" name="region" class="form-select" required>
                                    <option value="">Select region</option>
                                    @foreach ($deliveryLocations as $region => $districts)
                                        <option value="{{ $region }}" @selected(old('region', $profile?->delivery_region) === $region)>{{ $region }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="delivery-district" class="form-label">District / City</label>
                                <select id="delivery-district" name="district" class="form-select" required disabled>
                                    <option value="">Select district / city</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="delivery-area" class="form-label">Area / Town</label>
                                <select id="delivery-area" name="area" class="form-select" required disabled>
                                    <option value="">Select area / town</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="delivery-landmark" class="form-label">Landmark / Building</label>
                                <input id="delivery-landmark" type="text" name="landmark" class="form-control" value="{{ old('landmark', $profile?->delivery_landmark) }}" placeholder="e.g. near Total Petrol Station" required>
                            </div>
                            <div class="col-md-6">
                                <label for="delivery-address" class="form-label">Street / Extra Directions <span class="text-white-50">(optional)</span></label>
                                <input id="delivery-address" type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Street, plot, apartment, floor...">
                            </div>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="save_location" value="1" id="save-location" @checked(old('save_location', true))>
                            <label class="form-check-label small" for="save-location">Save this as my default delivery location</label>
                        </div>
                    </div>

                    <hr class="text-secondary">
                    <h6 class="fw-bold mb-2">Payment</h6>
                    <p class="text-white-50 small mb-3">
                        A payment prompt will be sent to your mobile money phone number above.
                        Approve it to complete your order.
                    </p>
                    <div class="d-flex gap-3 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="provider" id="mtn" value="MTN" required @checked(old('provider', 'MTN') === 'MTN')>
                            <label class="form-check-label" for="mtn">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/New-mtn-logo.jpg/120px-New-mtn-logo.jpg"
                                     alt="MTN MoMo" style="height:22px;"> MTN MoMo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="provider" id="airtel" value="Airtel" @checked(old('provider') === 'Airtel')>
                            <label class="form-check-label" for="airtel">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Airtel_logo.svg/120px-Airtel_logo.svg.png"
                                     alt="Airtel Money" style="height:22px;"> Airtel Money
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sh-orange w-100 mt-3 btn-lg">
                        <i class="bi bi-phone me-2"></i>Pay with PesaJet
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="sh-summary p-4">
                <h5 class="mb-4">Order Summary</h5>
                @foreach ($products as $item)
                    @php $qty = $cart[$item->id]['quantity']; $lineTotal = $item->price * $qty; @endphp
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset($item->image) }}"
                                 alt="" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px;">
                            <div>
                                <span class="small fw-medium">{{ $item->name }}</span>
                                <small class="d-block text-white-50">Qty: {{ $qty }}</small>
                            </div>
                        </div>
                        <span class="fw-medium">UGX {{ number_format($lineTotal) }}</span>
                    </div>
                @endforeach
                <hr style="border-color: rgba(255,255,255,0.15);">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Subtotal</span>
                    <span>UGX {{ number_format($total) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Delivery</span>
                    <span class="text-success">{{ $total >= 200000 ? 'Free' : 'UGX 20,000' }}</span>
                </div>
                <hr style="border-color: rgba(255,255,255,0.15);">
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span style="color: var(--sh-orange);">UGX {{ number_format($total >= 200000 ? $total : $total + 20000) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const locations = @json($deliveryLocations);
    const regionSelect = document.getElementById('delivery-region');
    const districtSelect = document.getElementById('delivery-district');
    const areaSelect = document.getElementById('delivery-area');
    const savedRegion = @json(old('region', $profile?->delivery_region));
    const savedDistrict = @json(old('district', $profile?->delivery_district));
    const savedArea = @json(old('area', $profile?->delivery_area));

    function populate(select, values, placeholder, selected = '') {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        values.forEach(value => {
            const option = new Option(value, value, false, value === selected);
            select.add(option);
        });
        select.disabled = values.length === 0;
    }

    function loadDistricts(selected = '') {
        const districts = locations[regionSelect.value] ? Object.keys(locations[regionSelect.value]) : [];
        populate(districtSelect, districts, 'Select district / city', selected);
        loadAreas(selected ? districtSelect.value : '');
    }

    function loadAreas(district = '') {
        const areas = locations[regionSelect.value]?.[district] ?? [];
        populate(areaSelect, areas, 'Select area / town', district === savedDistrict ? savedArea : '');
    }

    regionSelect.addEventListener('change', () => {
        populate(districtSelect, [], 'Select district / city');
        populate(areaSelect, [], 'Select area / town');
        loadDistricts();
    });

    districtSelect.addEventListener('change', () => loadAreas(districtSelect.value));

    if (regionSelect.value) loadDistricts(savedDistrict);
})();
</script>
@endpush
@endsection
