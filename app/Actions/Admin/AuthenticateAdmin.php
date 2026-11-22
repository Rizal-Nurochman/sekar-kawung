<?php

namespace App\Actions\Admin;

use Illuminate\Support\Facades\Auth;

class AuthenticateAdmin
{
    /**
     * Attempt to authenticate the admin using session guard.
     *
     * @param  array{email: string, password: string}  $credentials
     */
    public function handle(array $credentials, bool $remember = false): bool
    {
        if (Auth::attempt($credentials, $remember)) {
            request()->session()->regenerate();

            return true;
        }

        return false;
    }
}
