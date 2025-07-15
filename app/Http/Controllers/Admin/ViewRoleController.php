<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LdapUser;
use App\Services\LdapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ViewRoleController extends Controller
{
    protected $ldapService;

    public function __construct(LdapService $ldapService)
    {
        $this->ldapService = $ldapService;
    }

    public function index()
    {
        try {
            // Get all AD users
            $allUsers = $this->ldapService->getAllUsers();
            
            // Paginate the results
            $perPage = 20;
            $currentPage = request()->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $users = new LengthAwarePaginator(
                array_slice($allUsers, $offset, $perPage),
                count($allUsers),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            // Get statistics
            $totalUsers = count($allUsers);
            $adminUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'admin'));
            $regularUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'user'));
            $loggedInUsers = count(array_filter($allUsers, fn($user) => $user['has_logged_in']));
            
            return view('admin.viewrole.index', compact('users', 'totalUsers', 'adminUsers', 'regularUsers', 'loggedInUsers'));
            
        } catch (\Exception $e) {
            Log::error('Error loading AD users: ' . $e->getMessage());
            return back()->with('error', 'Failed to load Active Directory users. Please check your LDAP connection.');
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            
            if ($search) {
                $allUsers = $this->ldapService->searchUsers($search);
            } else {
                $allUsers = $this->ldapService->getAllUsers();
            }
            
            // Paginate the results
            $perPage = 20;
            $currentPage = request()->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $users = new LengthAwarePaginator(
                array_slice($allUsers, $offset, $perPage),
                count($allUsers),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            // Get statistics
            $totalUsers = count($allUsers);
            $adminUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'admin'));
            $regularUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'user'));
            $loggedInUsers = count(array_filter($allUsers, fn($user) => $user['has_logged_in']));
            
            return view('admin.viewrole.index', compact('users', 'totalUsers', 'adminUsers', 'regularUsers', 'loggedInUsers', 'search'));
            
        } catch (\Exception $e) {
            Log::error('Error searching AD users: ' . $e->getMessage());
            return back()->with('error', 'Failed to search Active Directory users. Please try again.');
        }
    }

    public function filterByRole(Request $request)
    {
        try {
            $role = $request->input('role');
            
            $allUsers = $this->ldapService->getAllUsers();
            
            // Filter by role if specified
            if ($role && $role !== 'all') {
                $allUsers = array_filter($allUsers, fn($user) => $user['role'] === $role);
            }
            
            // Paginate the results
            $perPage = 20;
            $currentPage = request()->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            
            $users = new LengthAwarePaginator(
                array_slice($allUsers, $offset, $perPage),
                count($allUsers),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            // Get statistics
            $totalUsers = count($allUsers);
            $adminUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'admin'));
            $regularUsers = count(array_filter($allUsers, fn($user) => $user['role'] === 'user'));
            $loggedInUsers = count(array_filter($allUsers, fn($user) => $user['has_logged_in']));
            
            return view('admin.viewrole.index', compact('users', 'totalUsers', 'adminUsers', 'regularUsers', 'loggedInUsers', 'role'));
            
        } catch (\Exception $e) {
            Log::error('Error filtering AD users: ' . $e->getMessage());
            return back()->with('error', 'Failed to filter Active Directory users. Please try again.');
        }
    }

    public function show($username)
    {
        try {
            // Get the specific user from LDAP
            $user = $this->ldapService->getUserByUsername($username);
            
            if (!$user) {
                return back()->with('error', 'User not found in Active Directory.');
            }

            // Get user's group memberships
            $groups = $user['groups'] ?? [];
            
            // Get user's login history if available
            $loginHistory = [
                'last_login' => $user['last_login_at'] ?? null,
                'logon_count' => $user['logon_count'] ?? 0,
                'bad_password_count' => $user['bad_password_count'] ?? 0,
                'password_last_set' => $user['password_last_set'] ?? null,
                'account_expires' => $user['account_expires'] ?? null,
            ];

            return view('admin.viewrole.show', compact('user', 'groups', 'loginHistory'));
            
        } catch (\Exception $e) {
            Log::error('Error viewing AD user details: ' . $e->getMessage());
            return back()->with('error', 'Failed to load user details. Please try again.');
        }
    }

    public function resetPassword(Request $request, $username)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ], [
                'password.required' => 'The password field is required.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            // Use LdapRecord to find the user in AD
            $ldapUser = \LdapRecord\Models\ActiveDirectory\User::where('samaccountname', '=', $username)->first();
            
            if (!$ldapUser) {
                return back()->withErrors(['error' => 'User not found in Active Directory.'])->withInput();
            }

            try {
                // Set the new password in AD
                $ldapUser->unicodepwd = $request->password;
                $ldapUser->save();
                
                return back()->with('success', 'Password has been reset successfully.');
            } catch (\LdapRecord\LdapRecordException $e) {
                Log::error('LDAP Password Reset Error: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Failed to reset password in Active Directory. ' . $e->getMessage()])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Password Reset Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An unexpected error occurred while resetting the password.'])->withInput();
        }
    }
} 