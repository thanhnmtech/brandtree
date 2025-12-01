<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // User exists with Google ID, log them in
                Auth::login($user);
                return redirect()->route('dashboard');
            }

            // Check if user exists with this email
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // User exists with email, link Google account
                $user->google_id = $googleUser->id;
                $user->email_verified_at = now(); // Auto-verify email for Google users
                $user->save();

                Auth::login($user);
                return redirect()->route('dashboard')->with('success', __('messages.google.linked_success'));
            }

            // Create new user
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => Hash::make(Str::random(24)), // Random password for Google users
                'email_verified_at' => now(), // Auto-verify email for Google users
            ]);

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', __('messages.google.created_success'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('login')->withErrors(['email' => __('messages.google.auth_failed')]);
        }
    }
}
