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
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = null;
    
        // Vérifie si l'URL contient un paramètre "user"
        if ($request->has('profile_user')) {
            $userId = $request->input('profile_user');
            $user = User::find($userId);
        }
    // dd($user);
        $cachedUrl = Cache::get('profile_link_' . auth()->id());
    
        if (!$cachedUrl) {
            $url = URL::temporarySignedRoute(
                'profile.edit',
                now()->addHour(),
                ['profile_user' => auth()->id()]
            );
    
            Cache::put('profile_link_' . auth()->id(), $url, now()->addHour());
        } else {
            $url = $cachedUrl;
        }
    
        $qrCode = QrCode::size(100)->generate($url);
    
        return view('profile.edit', [
            'user' => $user ?: $request->user(),
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
