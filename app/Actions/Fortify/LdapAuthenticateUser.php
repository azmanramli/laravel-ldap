<?php

namespace App\Actions\Fortify;

use App\Models\LdapUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use LdapRecord\Connection;
use LdapRecord\Container;

class LdapAuthenticateUser
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            // Attempt LDAP authentication
            $ldapUser = $this->authenticateLdap($username, $password);
            
            if (!$ldapUser) {
                throw ValidationException::withMessages([
                    'username' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Get or create user in local database
            $user = $this->getOrCreateLdapUser($username, $ldapUser);
            
            // Log in the user
            Auth::login($user, $request->boolean('remember'));
            
            return $user;

        } catch (\Exception $e) {
            Log::error('LDAP Authentication Error: ' . $e->getMessage());
            
            throw ValidationException::withMessages([
                'username' => ['Authentication failed. Please try again.'],
            ]);
        }
    }

    private function authenticateLdap($username, $password)
    {
        $connection = Container::getDefaultConnection();
        
        $attribute = config('ldap.username_attribute', 'samaccountname');
        
        try {
            // Search for user in LDAP
            $query = $connection->query();
            $query->where($attribute, '=', $username);
            
            $ldapUser = $query->first();
            
            if (!$ldapUser) {
                Log::info("LDAP user not found: {$username}");
                return null;
            }

            // Get the user's DN for binding
            $userDn = null;
            if (is_array($ldapUser)) {
                $userDn = $ldapUser['dn'] ?? null;
            } else {
                $userDn = $ldapUser->getDn();
            }
            
            if (!$userDn) {
                Log::error("Could not get DN for user: {$username}");
                return null;
            }

            // Attempt to bind with user credentials
            $connection->connect($userDn, $password);
            
            Log::info("LDAP authentication successful for user: {$username}");
            return $ldapUser;
            
        } catch (\Exception $e) {
            Log::error("LDAP authentication failed for user {$username}: " . $e->getMessage());
            return null;
        }
    }

    private function getOrCreateLdapUser($username, $ldapUser)
    {
        // Check if user exists in local database
        $user = LdapUser::where('username', $username)->first();

        if (!$user) {
            // Create new user in local database with default role 'user'
            $user = LdapUser::create([
                'username' => $username,
                'role' => 'user',
            ]);
        }
        // Never update role from LDAP; manage role manually in DB
        return $user;
    }

    private function determineRole($ldapUser)
    {
        try {
            // Check if user is in admin group
            $adminGroups = config('ldap.admin_groups', ['Domain Admins', 'Enterprise Admins']);
            
            foreach ($adminGroups as $group) {
                if ($this->isUserInGroup($ldapUser, $group)) {
                    Log::info("User is member of admin group: {$group}");
                    return 'admin';
                }
            }
            
            Log::info("User is not member of any admin groups, assigning 'user' role");
            return 'user';
            
        } catch (\Exception $e) {
            Log::error("Error determining user role: " . $e->getMessage());
            return 'user'; // Default to user role on error
        }
    }

    private function isUserInGroup($ldapUser, $groupName)
    {
        try {
            $connection = Container::getDefaultConnection();
            $query = $connection->query();
            
            // Search for the group
            $group = $query->where('cn', '=', $groupName)->first();
            
            if (!$group) {
                return false;
            }
            
            // Check if user is member of the group
            // Handle both array and object LDAP results
            if (is_array($ldapUser)) {
                $memberOf = $ldapUser['memberof'] ?? [];
            } else {
                $memberOf = $ldapUser->getAttribute('memberof') ?? [];
            }
            
            if (is_array($memberOf)) {
                foreach ($memberOf as $member) {
                    if (str_contains($member, 'CN=' . $groupName . ',')) {
                        return true;
                    }
                }
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error checking group membership: ' . $e->getMessage());
            return false;
        }
    }
} 