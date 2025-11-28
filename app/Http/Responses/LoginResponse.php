<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContracts;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContracts;

class LoginResponse implements LoginResponseContracts, TwoFactorLoginResponseContracts
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->role === UserRole::Admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('customer.homepage'));
    }
}
