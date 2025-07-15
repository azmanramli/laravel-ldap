<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Container;
use App\Models\LdapUser;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->username;
        $password = $request->password;

        try {
            $ldap = Container::getDefaultConnection();
            $ldap->auth()->attempt("yourdomain\\$username", $password, $bindAsUser = true);
        } catch (\LdapRecord\Auth\BindException $e) {
            return back()->withErrors(['login_error' => 'Invalid credentials']);
        }

        $localUser = LdapUser::where('username', $username)->first();

        if (!$localUser) {
            return back()->withErrors(['login_error' => 'Access denied: User not registered in system']);
        }

        Auth::login($localUser);

        return match ($localUser->role) {
            'admin' => redirect('/admin/dashboard'),
            'staff' => redirect('/staff/dashboard'),
            default => redirect('/home'),
        };
    }
}
