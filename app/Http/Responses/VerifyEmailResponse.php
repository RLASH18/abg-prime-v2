<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
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

        return redirect()->intended(route('customer.homepage.index'));
    }
}
