@extends('layouts.public')

@section('title', 'My Profile — ' . config('app.name', 'Shoe Haven'))

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('home') }}"
           class="d-flex align-items-center justify-content-center flex-shrink-0 text-decoration-none rounded-circle fw-bold"
           style="width: 40px; height: 40px; background: #fff3e6; color: var(--sh-orange); transition: background .15s;"
           onmouseover="this.style.background='#ffe0cc'" onmouseout="this.style.background='#fff3e6'">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0">My Profile</h2>
    </div>

    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                         style="width: 90px; height: 90px; font-size: 2.2rem; background: var(--sh-orange);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->profile?->full_name ?? $user->name }}</h5>
                    <small class="text-muted">{{ $user->email }}</small>
                    @if ($user->profile?->phone)
                        <div class="mt-1"><small class="text-muted"><i class="bi bi-phone me-1"></i>{{ $user->profile->phone }}</small></div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Member since</span>
                        <span class="fw-medium">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Orders</span>
                        <span class="fw-medium">{{ $ordersCount }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Wishlist</span>
                        <span class="fw-medium">{{ $wishlistCount }}</span>
                    </div>
                    <hr>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('orders.index') }}" class="text-decoration-none flex-fill">
                            <div class="d-flex align-items-center justify-content-center gap-2 py-2 px-3 rounded-3 fw-medium small"
                                 style="background: #fff3e6; color: var(--sh-orange); transition: background .15s;"
                                 onmouseover="this.style.background='#ffe0cc'" onmouseout="this.style.background='#fff3e6'">
                                <i class="bi bi-bag fs-6"></i>
                                <span>{{ $ordersCount }} Orders</span>
                            </div>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="text-decoration-none flex-fill">
                            <div class="d-flex align-items-center justify-content-center gap-2 py-2 px-3 rounded-3 fw-medium small"
                                 style="background: #fce4ec; color: #e74c3c; transition: background .15s;"
                                 onmouseover="this.style.background='#f8bbd0'" onmouseout="this.style.background='#fce4ec'">
                                <i class="bi bi-heart fs-6"></i>
                                <span>{{ $wishlistCount }} Saved</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            @if ($recentOrders->isNotEmpty())
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Recent Orders</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($recentOrders as $order)
                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center justify-content-between p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <span class="fw-bold small">#{{ $order->id }}</span>
                                        <small class="d-block text-muted">{{ $order->created_at->format('M d') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-medium small">UGX {{ number_format($order->total_amount) }}</span>
                                        @php
                                            $colors = ['pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'];
                                        @endphp
                                        <span class="badge bg-{{ $colors[$order->status] ?? 'secondary' }} d-block mt-1" style="font-size: .6rem;">{{ ucfirst($order->status) }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if ($ordersCount > 5)
                        <div class="card-footer bg-white text-center py-2">
                            <a href="{{ route('orders.index') }}" class="text-decoration-none small">View all orders <i class="bi bi-chevron-right"></i></a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Main --}}
        <div class="col-lg-8">
            {{-- Profile Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Update your account's profile information and email address.</p>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="w-100" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-1" :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="w-100" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-1" :messages="$errors->get('email')" />

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-muted small mb-0">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification" class="btn btn-link btn-sm p-0 text-decoration-none">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-success small mt-1 mb-0">{{ __('A new verification link has been sent to your email address.') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-sh-orange">Save Changes</button>
                            @if (session('status') === 'profile-updated')
                                <span class="text-success small fw-medium"><i class="bi bi-check-circle"></i> Saved</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Default Delivery Location --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Default Delivery Location</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Save your usual delivery area so checkout is faster. You can change it at any time.</p>
                    <form method="post" action="{{ route('profile.update-details') }}" id="delivery-location-form">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="full_name" value="{{ old('full_name', $user->profile?->full_name ?? $user->name) }}">
                        <input type="hidden" name="phone" value="{{ old('phone', $user->profile?->phone) }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="profile-region" class="form-label">Region</label>
                                <select id="profile-region" name="delivery_region" class="form-select" required>
                                    <option value="">Select region</option>
                                    @foreach ($deliveryLocations as $region => $districts)
                                        <option value="{{ $region }}" @selected(old('delivery_region', $user->profile?->delivery_region) === $region)>{{ $region }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="profile-district" class="form-label">District / City</label>
                                <select id="profile-district" name="delivery_district" class="form-select" required disabled><option value="">Select district / city</option></select>
                            </div>
                            <div class="col-md-4">
                                <label for="profile-area" class="form-label">Area / Town</label>
                                <select id="profile-area" name="delivery_area" class="form-select" required disabled><option value="">Select area / town</option></select>
                            </div>
                            <div class="col-12">
                                <label for="profile-landmark" class="form-label">Landmark / Building</label>
                                <input id="profile-landmark" name="delivery_landmark" class="form-control" value="{{ old('delivery_landmark', $user->profile?->delivery_landmark) }}" placeholder="e.g. near Total Petrol Station" required>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-3">
                            <button type="submit" class="btn btn-sh-orange"><i class="bi bi-geo-alt me-1"></i>Save Delivery Location</button>
                            @if (session('status') === 'details-updated')
                                <span class="text-success small fw-medium"><i class="bi bi-check-circle"></i> Saved</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Contact Details --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-telephone me-2"></i>Contact Details</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Update your phone number and display name for faster checkout.</p>

                    <form method="post" action="{{ route('profile.update-details') }}">
                        @csrf
                        @method('patch')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input-label for="full_name" :value="__('Full Name')" />
                                <x-text-input id="full_name" name="full_name" type="text" class="w-100" :value="old('full_name', $user->profile?->full_name ?? $user->name)" autocomplete="name" />
                                <x-input-error class="mt-1" :messages="$errors->get('full_name')" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" name="phone" type="text" class="w-100" :value="old('phone', $user->profile?->phone)" placeholder="+256 700 000 000" autocomplete="tel" />
                                <x-input-error class="mt-1" :messages="$errors->get('phone')" />
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-3">
                            <button type="submit" class="btn btn-sh-orange">Save Details</button>
                            @if (session('status') === 'details-updated')
                                <span class="text-success small fw-medium"><i class="bi bi-check-circle"></i> Saved</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i>Update Password</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Ensure your account is using a long, random password to stay secure.</p>

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                            <x-text-input id="update_password_current_password" name="current_password" type="password" class="w-100" autocomplete="current-password" />
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="update_password_password" :value="__('New Password')" />
                            <x-text-input id="update_password_password" name="password" type="password" class="w-100" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                        </div>

                        <div class="mb-3">
                            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-100" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-sh-orange">Update Password</button>
                            @if (session('status') === 'password-updated')
                                <span class="text-success small fw-medium"><i class="bi bi-check-circle"></i> Saved</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Account</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
                        <i class="bi bi-trash me-1"></i> Delete Account
                    </button>

                    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form method="post" action="{{ route('profile.destroy') }}" class="modal-content">
                                @csrf
                                @method('delete')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">{{ __('Are you sure you want to delete your account?') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small text-muted">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}</p>
                                    <div class="mb-3">
                                        <x-input-label for="password" :value="__('Password')" />
                                        <x-text-input id="password" name="password" type="password" class="w-100" placeholder="{{ __('Enter your password') }}" />
                                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Delete My Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const locations = @json($deliveryLocations);
    const region = document.getElementById('profile-region');
    const district = document.getElementById('profile-district');
    const area = document.getElementById('profile-area');
    if (!region || !district || !area) return;
    const selectedDistrict = @json(old('delivery_district', $user->profile?->delivery_district));
    const selectedArea = @json(old('delivery_area', $user->profile?->delivery_area));

    function fill(select, values, placeholder, selected = '') {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        values.forEach(v => select.add(new Option(v, v, false, v === selected)));
        select.disabled = values.length === 0;
    }
    function districts() {
        fill(district, Object.keys(locations[region.value] || {}), 'Select district / city', selectedDistrict);
        areas();
    }
    function areas() {
        fill(area, locations[region.value]?.[district.value] || [], 'Select area / town', district.value === selectedDistrict ? selectedArea : '');
    }
    region.addEventListener('change', () => { fill(district, [], 'Select district / city'); fill(area, [], 'Select area / town'); districts(); });
    district.addEventListener('change', areas);
    if (region.value) districts();
})();
</script>
@endpush
@endsection