<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\LdapAuthenticateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LdapAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, LdapAuthenticateUser $authenticateUser)
    {
        Log::info('Login route hit - Method: ' . $request->method() . ' - URL: ' . $request->url());
        Log::info('Request data: ' . json_encode($request->only(['username', 'password'])));
        
        try {
            $user = $authenticateUser($request);
            
            $request->session()->regenerate();
            
            // Debug information
            Log::info('Login successful for user: ' . $user->username);
            Log::info('User role: ' . $user->role);
            Log::info('Auth check: ' . (Auth::check() ? 'true' : 'false'));
            Log::info('Current user: ' . (Auth::user() ? Auth::user()->username : 'none'));
            
            // Determine the redirect URL based on user role
            $redirectUrl = $user->role === 'admin' ? '/admin/home' : '/home';
            
            // If there's an intended URL, use it; otherwise use our default
            $finalRedirect = $request->session()->get('url.intended', $redirectUrl);
            
            // Clear the intended URL from session
            $request->session()->forget('url.intended');
            
            Log::info('User role: ' . $user->role . ' - Redirecting to: ' . $finalRedirect);
            
            return redirect($finalRedirect);
            
        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return back()->withErrors([
                'username' => 'The provided credentials are incorrect.',
            ])->withInput($request->only('username'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
} 