<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Cache;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {


        $cachedUrl = Cache::get('profile_link_' . auth()->id());

        if (!$cachedUrl) {
            // Générer un nouveau lien
            $url = URL::temporarySignedRoute(
                'profile.edit',
                now()->addHour(),
                ['user' => auth()->id()]
            );

            // Stocker le lien en cache avec une durée de vie d'une heure
            Cache::put('profile_link_' . auth()->id(), $url, now()->addHour());
        } else {
            $url = $cachedUrl;
        }

        $qrCode = QrCode::size(100)->generate($url);
        // dd(''. $qrCode);
        return view('profile.edit', [
            'user' => $request->user(),
            'url' => $url,
            'qrCode' => $qrCode,
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
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
