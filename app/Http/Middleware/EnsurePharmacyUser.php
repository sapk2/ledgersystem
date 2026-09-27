<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePharmacyUser
{
    /**
     * The pharmacy application is only available to signed-in users of an active pharmacy.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        abort_unless($user !== null, 403);

        if (! $user->hasActivePharmacy()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your pharmacy account is currently inactive. Please contact the administrator.']);
        }

        return $next($request);
    }
}
