<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended('/');

        } catch (\Exception $e) {
            Log::error('Google OAuth failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')
                ->with('status', 'Google login failed. Please try again.');
        }
    }
}
