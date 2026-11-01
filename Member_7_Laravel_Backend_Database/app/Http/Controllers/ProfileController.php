<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Support\DeliveryLocations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $recentOrders = $user->orders()->with('items')->latest()->limit(5)->get();
        $ordersCount = $user->orders()->count();
        $wishlistCount = $user->wishlist()->count();

        return view('profile.edit', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'ordersCount' => $ordersCount,
            'wishlistCount' => $wishlistCount,
            'deliveryLocations' => DeliveryLocations::data(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's additional profile details (phone, full_name).
     */
    public function updateDetails(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^[\+\d\s\-\(\)]+$/'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'delivery_region' => ['nullable', 'string', 'max:64'],
            'delivery_district' => ['nullable', 'string', 'max:128'],
            'delivery_area' => ['nullable', 'string', 'max:128'],
            'delivery_landmark' => ['nullable', 'string', 'max:255'],
        ]);

        if (($data['delivery_region'] ?? null) || ($data['delivery_district'] ?? null) || ($data['delivery_area'] ?? null)) {
            if (! DeliveryLocations::isValid($data['delivery_region'] ?? '', $data['delivery_district'] ?? '', $data['delivery_area'] ?? '')) {
                return back()->withErrors(['delivery_area' => 'Please select a valid delivery region, district and area.'])->withInput();
            }
        }

        $profile = $request->user()->profile;

        if (! $profile) {
            $profile = new Profile(['user_id' => $request->user()->id]);
        }

        $profile->fill($data);
        $profile->save();

        return Redirect::route('profile.edit')->with('status', 'details-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->orders()->exists()) {
            return back()->withErrors([
                'userDeletion' => 'This account has order history and cannot be deleted. Please contact support if you need your account closed.',
            ]);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
