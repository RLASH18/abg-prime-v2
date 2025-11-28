<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    protected const PROVIDERS = ['google', 'facebook'];

    /**
     * Redirect the user to the provider's authentication page.
     */
    public function redirect(string $provider)
    {
        if (! in_array($provider, self::PROVIDERS)) {
            abort(404);
        }

        return Socialite::driver($provider)
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the provider's callback and login/register the user.
     */
    public function callback(string $provider)
    {
        if (! in_array($provider, self::PROVIDERS)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed or was cancelled.');
        }

        $idColumn = $provider . '_id';

        $user = User::where($idColumn, $socialUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link existing user
                $user->update([
                    $idColumn => $socialUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            } else {
                // Create NEW User
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(32)),
                    'role' => UserRole::Customer,
                    'email_verified_at' => now(),
                    $idColumn => $socialUser->getId(),
                ]);
            }
        }

        $user->profile()->firstOrCreate([
            'user_id' => $user->id
        ]);

        Auth::login($user);

        return redirect()->route('customer.homepage');
    }
}
