<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\User;

class AdminUser
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('voyager.login');
        }

        $user = Auth::user();
        if ($user instanceof User && $user->role && $user->role->name === 'admin') {
            return $next($request);
        }

        return redirect()->route('voyager.login');
    }
}
