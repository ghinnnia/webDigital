<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }
        
        $userRole = $request->user()->role;
        
        // Debug info (hapus setelah fix)
        if (app()->environment('local')) {
            \Log::info('CheckRole Middleware:', [
                'user_id' => $request->user()->id,
                'user_role' => $userRole,
                'allowed_roles' => $roles,
                'url' => $request->url(),
            ]);
        }
        
        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access. Your role: ' . $userRole . '. Required roles: ' . implode(', ', $roles));
        }
        
        return $next($request);
    }
}