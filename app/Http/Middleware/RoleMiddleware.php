<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
  public function handle(Request $request, Closure $next, ...$roles)
{
    \Log::info('RoleMiddleware checking', [
        'user_id' => Auth::id(),
        'user_role' => Auth::user()->role ?? 'none',
        'required_roles' => $roles,
        'path' => $request->path(),
        'url' => $request->url()
    ]);

    // 1. PERIKSA: Apakah user sudah login?
    if (!Auth::check()) {
        \Log::warning('User not authenticated for role middleware');
        return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
    }

    // 2. PERIKSA: Apakah role user ada di dalam daftar role yang diizinkan?
    if (!in_array(Auth::user()->role, $roles)) {
        \Log::warning('User role not authorized', [
            'user_role' => Auth::user()->role,
            'required_roles' => $roles
        ]);
        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini. Role Anda: ' . Auth::user()->role);
    }

    // 3. LANJUTKAN: Jika semua pengecekan lolos, lanjutkan request
    \Log::info('RoleMiddleware passed for user', [
        'user_id' => Auth::id(),
        'user_role' => Auth::user()->role
    ]);
    return $next($request);
}
}