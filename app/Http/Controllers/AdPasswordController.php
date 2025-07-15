<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdPasswordController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Get the authenticated user's username
            $username = Auth::user()->username;

            // Find the user in Active Directory
            $ldapUser = User::where('samaccountname', '=', $username)->first();

            if (!$ldapUser) {
                return back()->withErrors(['error' => 'User not found in Active Directory.']);
            }

            // Attempt to change the password
            $ldapUser->unicodepwd = [
                $request->current_password,
                $request->password
            ];

            if ($ldapUser->save()) {
                Log::info("Password successfully changed in AD for user: {$username}");
                return back()->with('success', 'Your Active Directory password has been changed successfully.');
            }

        } catch (\LdapRecord\Exceptions\InsufficientAccessException $e) {
            Log::error("Insufficient permissions to change AD password for user {$username}: " . $e->getMessage());
            return back()->withErrors(['error' => 'You do not have permission to change the password.']);
        } catch (\LdapRecord\Exceptions\ConstraintException $e) {
            Log::error("Password policy violation for user {$username}: " . $e->getMessage());
            return back()->withErrors(['error' => 'The new password does not meet the password policy requirements.']);
        } catch (\Exception $e) {
            Log::error("Failed to change AD password for user {$username}: " . $e->getMessage());
            
            // Check for specific AD error codes
            $error = $e->getDetailedError();
            if ($error && strpos($error->getDiagnosticMessage(), '52e') !== false) {
                return back()->withErrors(['error' => 'Current password is incorrect.']);
            }
            
            return back()->withErrors(['error' => 'Failed to change password. Please try again or contact support.']);
        }

        return back()->withErrors(['error' => 'An unexpected error occurred.']);
    }
} 