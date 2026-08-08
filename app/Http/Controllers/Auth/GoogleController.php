<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'participant',
            ]);
        } elseif (! $user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user, remember: true);

        // Admin/operator go straight to the admin dashboard.
        if ($user->canManageDraw()) {
            return redirect()->route('admin.dashboard');
        }

        // Participant: auto-register with a ticket number if registration
        // is open and they don't have one yet.
        if (! $user->participant) {
            if (! Setting::registrationOpen()) {
                return redirect()->route('registration.closed');
            }

            Participant::create([
                'user_id' => $user->id,
                'ticket_number' => Participant::generateUniqueTicketNumber(),
                'registered_at' => now(),
            ]);
        }

        return redirect()->route('participant.dashboard');
    }
}
