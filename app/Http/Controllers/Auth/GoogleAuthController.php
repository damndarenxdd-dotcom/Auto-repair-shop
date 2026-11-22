<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $clientId = trim((string) config('services.google.client_id'));
        $clientSecret = trim((string) config('services.google.client_secret'));

        if ($this->hasPlaceholderGoogleCredential($clientId) || $this->hasPlaceholderGoogleCredential($clientSecret) || $clientId === '' || $clientSecret === '') {
            return redirect()->route('login')->with('error', 'Google OAuth is not configured yet. Add your real Google client ID and secret in the environment settings.');
        }

        return Socialite::driver('google')->redirect();
    }

    protected function hasPlaceholderGoogleCredential(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $normalized = strtolower($value);

        return str_contains($normalized, 'your_')
            || str_contains($normalized, 'example')
            || str_contains($normalized, 'replace_me')
            || str_contains($normalized, 'placeholder');
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $exception) {
            return redirect()->route('login')->with('error', 'The Google login session expired. Please try again.');
        } catch (\Exception $exception) {
            return redirect()->route('login')->with('error', 'Google sign-in could not be completed. Please try again or use your email and password.');
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->with('error', 'Google did not return a valid email address for this account.');
        }

        $user = User::where('email', $email)->first();

        if (! $user && Schema::hasColumn('users', 'google_id')) {
            $user = User::where('google_id', $googleUser->getId())->first();
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $email,
                'password' => Hash::make(str()->random(24)),
                'email_verified_at' => now(),
                'role_id' => 4,
                'google_id' => Schema::hasColumn('users', 'google_id') ? $googleUser->getId() : null,
            ]);
        }

        $user->name = $user->name ?: ($googleUser->getName() ?: 'Google User');

        if (Schema::hasColumn('users', 'google_id')) {
            $user->google_id = $user->google_id ?? $googleUser->getId();
        }

        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }
}
