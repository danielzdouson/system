<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is authenticated and has member role
        if (!$user || !$user->isMember()) {
            abort(403, 'Access denied. Member access required.');
        }
        
        // Check if user has a linked member account
        if (!$user->member) {
            abort(403, 'No member account linked to your user account.');
        }
        
        return $next($request);
    }
}
