<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'profile' => $request->user()->profile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Update user models fields (name, email)
        $userFields = ['name', 'email'];
        $userData = array_intersect_key($validated, array_flip($userFields));

        $request->user()->fill($userData);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Update UserProfile model fields
        $profileFields = [
            'address',
            'contact_number',
            'gender',
            'birth_date'
        ];
        $profileData = array_intersect_key($validated, array_flip($profileFields));

        if (! empty($profileData)) {
            $request->user()->profile()->updateOrCreate(
                ['user_id' => $request->user()->id],
                $profileData
            );
        }

        // Dynamically redirect based on the current route prefix
        $currentRouteName = $request->route()->getName();
        $routePrefix = str_starts_with($currentRouteName, 'admin.') ? 'admin.' : 'customer.';

        return to_route($routePrefix . 'profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
