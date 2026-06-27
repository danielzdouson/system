<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allow only staff roles into the admin back office.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $staffRoles = ['admin', 'super_admin', 'loans_officer', 'treasurer'];

        if (! $user || ! in_array($user->role, $staffRoles, true)) {
            abort(403, 'Access denied. Staff access required.');
        }

        return $next($request);
    }
}
