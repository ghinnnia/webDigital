<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function show()
    {
        \Log::info('LoginController@show called', [
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'path' => request()->path(),
        ]);
        
        // If user is already logged in, redirect to their dashboard
        if (Auth::check()) {
            $user = Auth::user();
            \Log::info('User already authenticated in LoginController@show', [
                'user_id' => $user->id,
                'user_role' => $user->role,
            ]);
            
            // Only redirect if user has valid role
            if (!empty($user->role)) {
                return $this->redirectToRolePage($user);
            }
            // If user doesn't have role, logout and show login page
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        
        return view('login.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        \Log::info('LoginController@login called', [
            'email' => $request->email,
            'request_method' => $request->method(),
        ]);
        
        try {
            // Validate the request
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'password.required' => 'Password wajib diisi',
                
            ]);

            // Attempt to log the user in
            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                
                // Check if user is active (if you have an 'active' field)
                if (isset($user->active) && !$user->active) {
                    Auth::logout();
                    throw ValidationException::withMessages([
                        'email' => ['Akun Anda tidak aktif. Silakan hubungi administrator.'],
                    ]);
                }
                
                // Log successful login
                \Log::info('User logged in', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                ]);

                // Redirect based on role
                return $this->redirectToRolePage($user);
            }

            // If authentication fails
            throw ValidationException::withMessages([
                'email' => ['Email atau password yang Anda masukkan salah.'],
            ]);

        } catch (ValidationException $e) {
            // Re-throw validation exceptions
            throw $e;
        } catch (\Exception $e) {
            // Log other exceptions
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => ['Terjadi kesalahan saat login. Silakan coba lagi.'],
            ]);
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectToRolePage($user)
    {
        $redirectRoutes = [
            'admin' => 'admin.beranda',
            'finance' => 'finance.beranda',
            'hr' => 'hr.home',
            'karyawan' => 'karyawan.home',
            'general_manager' => 'general_manajer.home',
            'manager_divisi' => 'manager_divisi.home',
            'owner' => 'owner.home',
        ];

        $routeName = $redirectRoutes[$user->role] ?? 'login';
        
        return redirect()->route($routeName);
    }

    /**
     * Log the user out of the application
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Log logout
            if ($user) {
                \Log::info('User logged out', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                ]);
            }

            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/')->with('success', 'Anda telah berhasil logout.');
            
        } catch (\Exception $e) {
            \Log::error('Logout error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            
            // Still try to logout even if there's an error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/')->with('error', 'Terjadi kesalahan saat logout.');
        }
    }
}