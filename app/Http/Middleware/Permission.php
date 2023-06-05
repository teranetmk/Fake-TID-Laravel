<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use App\Models\User;

class Permission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        if($user instanceof User && $user->hasPermission($permission)) {
            return $next($request);
        }

        return redirect()->route('no-permissions');
    }
}
