<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Usage: ->middleware('role:admin') or ->middleware('role:admin|staff')
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        $allowed = explode('|', $roles);

        if (! in_array($user->role, $allowed)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
