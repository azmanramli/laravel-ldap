<?php

namespace App\Services;

use LdapRecord\Connection;
use LdapRecord\Container;
use Illuminate\Support\Facades\Log;
use App\Models\LdapUser;

class LdapService
{
    protected $connection;
    protected $baseDn;
    protected $adminGroups;

    public function __construct()
    {
        $this->connection = Container::getDefaultConnection();
        $this->baseDn = config('ldap.connections.default.base_dn');
        $this->adminGroups = config('ldap.admin_groups', []);
    }

    /**
     * Get all users from Active Directory
     */
    public function getAllUsers()
    {
        try {
            $query = $this->connection->query();
            $query->in($this->baseDn);
            
            // More comprehensive search - look for any object with samaccountname
            // This will catch users in different OUs and with different object classes
            $query->where('samaccountname', '*');
            
            // Exclude disabled accounts (userAccountControl = 514 means ACCOUNTDISABLE)
            $query->where('userAccountControl', '!=', '514');
            
            // Get specific attributes
            $query->select([
                'samaccountname',
                'displayname',
                'mail',
                'memberof',
                'distinguishedname',
                'whencreated',
                'lastlogon',
                'pwdlastset',
                // Account Status & Security
                'useraccountcontrol',
                'accountexpires',
                'lockouttime',
                'pwdlastset',
                'logoncount',
                'badpwdcount',
                'lastlogoff',
                // Organizational Information
                'department',
                'title',
                'manager',
                'physicaldeliveryofficename',
                'company',
                'division',
                'employeeid',
                // Contact & Communication
                'telephonenumber',
                'mobile',
                'ipphone',
                'facsimiletelephonenumber',
                'homephone',
                'pager',
                // Technical Information
                'userprincipalname',
                'objectsid',
                'homedirectory',
                'profilepath',
                'scriptpath',
                'primarygroupid',
                'samaccounttype',
                'userworkstations'
            ]);

            $results = $query->get();

            $users = [];
            foreach ($results as $result) {
                $user = $this->formatUserData($result);
                if ($user) {
                    $users[] = $user;
                }
            }

            Log::info('LDAP Query found ' . count($users) . ' users');
            return $users;

        } catch (\Exception $e) {
            Log::error('LDAP Error: ' . $e->getMessage());
            
            // Fallback: try a simpler query
            try {
                return $this->getAllUsersFallback();
            } catch (\Exception $fallbackError) {
                Log::error('LDAP Fallback Error: ' . $fallbackError->getMessage());
                throw new \Exception('Failed to fetch users from Active Directory. Please check your LDAP connection and credentials.');
            }
        }
    }

    /**
     * Fallback method for getting users with simpler query
     */
    protected function getAllUsersFallback()
    {
        $query = $this->connection->query();
        $query->in($this->baseDn);
        
        // Even simpler query - just look for any object with samaccountname
        // This should catch all user accounts regardless of object class
        $query->where('samaccountname', '*');
        
        $query->select([
            'samaccountname',
            'displayname',
            'mail',
            'memberof',
            'distinguishedname',
            'whencreated',
            'lastlogon',
            'pwdlastset',
            // Account Status & Security
            'useraccountcontrol',
            'accountexpires',
            'lockouttime',
            'pwdlastset',
            'logoncount',
            'badpwdcount',
            'lastlogoff',
            // Organizational Information
            'department',
            'title',
            'manager',
            'physicaldeliveryofficename',
            'company',
            'division',
            'employeeid',
            // Contact & Communication
            'telephonenumber',
            'mobile',
            'ipphone',
            'facsimiletelephonenumber',
            'homephone',
            'pager',
            // Technical Information
            'userprincipalname',
            'objectsid',
            'homedirectory',
            'profilepath',
            'scriptpath',
            'primarygroupid',
            'samaccounttype',
            'userworkstations'
        ]);

        $results = $query->get();

        $users = [];
        foreach ($results as $result) {
            $user = $this->formatUserData($result);
            if ($user) {
                $users[] = $user;
            }
        }

        Log::info('LDAP Fallback Query found ' . count($users) . ' users');
        return $users;
    }

    /**
     * Search users in Active Directory
     */
    public function searchUsers($searchTerm)
    {
        try {
            // Clean and encode the search term
            $searchTerm = mb_convert_encoding($searchTerm, 'UTF-8', 'UTF-8');
            $searchTerm = iconv('UTF-8', 'UTF-8//IGNORE', $searchTerm);
            
            $query = $this->connection->query();
            $query->in($this->baseDn);
            
            // Search in multiple attributes
            $query->where('samaccountname', 'contains', $searchTerm);
            $query->orWhere('displayname', 'contains', $searchTerm);
            $query->orWhere('mail', 'contains', $searchTerm);
            $query->orWhere('givenname', 'contains', $searchTerm);
            $query->orWhere('sn', 'contains', $searchTerm);
            $query->orWhere('cn', 'contains', $searchTerm);
            $query->orWhere('department', 'contains', $searchTerm);
            $query->orWhere('title', 'contains', $searchTerm);
            $query->orWhere('company', 'contains', $searchTerm);

            $results = $query->get();

            $users = [];
            foreach ($results as $result) {
                $user = $this->formatUserData($result);
                if ($user) {
                    $users[] = $user;
                }
            }

            return $users;

        } catch (\Exception $e) {
            Log::error('LDAP Search Error: ' . $e->getMessage());
            
            // Fallback: try a simpler search query
            try {
                return $this->searchUsersFallback($searchTerm);
            } catch (\Exception $fallbackError) {
                Log::error('LDAP Search Fallback Error: ' . $fallbackError->getMessage());
                throw new \Exception('Failed to search users in Active Directory. Please try again.');
            }
        }
    }

    /**
     * Get a specific user by their username (samaccountname)
     */
    public function getUserByUsername($username)
    {
        try {
            $query = $this->connection->query();
            $query->in($this->baseDn);
            $query->where('samaccountname', '=', $username);
            
            // Get specific attributes
            $query->select([
                'samaccountname',
                'displayname',
                'mail',
                'memberof',
                'distinguishedname',
                'whencreated',
                'lastlogon',
                'pwdlastset',
                // Account Status & Security
                'useraccountcontrol',
                'accountexpires',
                'lockouttime',
                'pwdlastset',
                'logoncount',
                'badpwdcount',
                'lastlogoff',
                // Organizational Information
                'department',
                'title',
                'manager',
                'physicaldeliveryofficename',
                'company',
                'division',
                'employeeid',
                // Contact & Communication
                'telephonenumber',
                'mobile',
                'ipphone',
                'facsimiletelephonenumber',
                'homephone',
                'pager',
                // Technical Information
                'userprincipalname',
                'objectsid',
                'homedirectory',
                'profilepath',
                'scriptpath',
                'primarygroupid',
                'samaccounttype',
                'userworkstations'
            ]);

            $result = $query->first();
            
            if (!$result) {
                return null;
            }

            return $this->formatUserData($result);

        } catch (\Exception $e) {
            Log::error('LDAP Error getting user: ' . $e->getMessage());
            throw new \Exception('Failed to fetch user from Active Directory. Please check your LDAP connection and credentials.');
        }
    }

    /**
     * Fallback method for searching users with simpler query
     */
    protected function searchUsersFallback($searchTerm)
    {
        // Clean and encode the search term
        $searchTerm = mb_convert_encoding($searchTerm, 'UTF-8', 'UTF-8');
        $searchTerm = iconv('UTF-8', 'UTF-8//IGNORE', $searchTerm);
        
        $query = $this->connection->query();
        $query->in($this->baseDn);
        
        // Simple search - look for samaccountname, displayname, or mail containing the search term
        $query->where('samaccountname', 'contains', $searchTerm);
        $query->orWhere('displayname', 'contains', $searchTerm);
        $query->orWhere('mail', 'contains', $searchTerm);
        
        $query->select([
            'samaccountname',
            'displayname',
            'mail',
            'memberof',
            'distinguishedname',
            'whencreated',
            'lastlogon',
            'pwdlastset',
            // Account Status & Security
            'useraccountcontrol',
            'accountexpires',
            'lockouttime',
            'pwdlastset',
            'logoncount',
            'badpwdcount',
            'lastlogoff',
            // Organizational Information
            'department',
            'title',
            'manager',
            'physicaldeliveryofficename',
            'company',
            'division',
            'employeeid',
            // Contact & Communication
            'telephonenumber',
            'mobile',
            'ipphone',
            'facsimiletelephonenumber',
            'homephone',
            'pager',
            // Technical Information
            'userprincipalname',
            'objectsid',
            'homedirectory',
            'profilepath',
            'scriptpath',
            'primarygroupid',
            'samaccounttype',
            'userworkstations'
        ]);

        $results = $query->get();

        $users = [];
        foreach ($results as $result) {
            $user = $this->formatUserData($result);
            if ($user) {
                $users[] = $user;
            }
        }

        Log::info('LDAP Search Fallback found ' . count($users) . ' users for term: ' . $searchTerm);
        return $users;
    }

    /**
     * Format LDAP user data
     */
    protected function formatUserData($ldapUser)
    {
        try {
            // Handle both array and object results
            $getAttribute = function($attribute) use ($ldapUser) {
                if (is_array($ldapUser)) {
                    return $ldapUser[$attribute] ?? null;
                } else {
                    return $ldapUser->getFirstAttribute($attribute);
                }
            };

            $getAttributeArray = function($attribute) use ($ldapUser) {
                if (is_array($ldapUser)) {
                    return $ldapUser[$attribute] ?? [];
                } else {
                    return $ldapUser->getAttribute($attribute) ?? [];
                }
            };

            // Helper function to safely encode strings
            $safeEncode = function($value) {
                if (is_string($value)) {
                    // Try to fix encoding issues
                    $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    // Remove any invalid UTF-8 characters
                    $value = iconv('UTF-8', 'UTF-8//IGNORE', $value);
                    return $value;
                }
                return $value;
            };

            $username = $safeEncode($getAttribute('samaccountname'));
            $displayName = $safeEncode($getAttribute('displayname'));
            $email = $safeEncode($getAttribute('mail'));
            $memberOf = $getAttributeArray('memberof');
            $distinguishedName = $safeEncode($getAttribute('distinguishedname'));
            $whenCreated = $getAttribute('whencreated');
            $lastLogon = $getAttribute('lastlogon');
            $pwdLastSet = $getAttribute('pwdlastset');

            // Account Status & Security
            $userAccountControl = $getAttribute('useraccountcontrol');
            $accountExpires = $getAttribute('accountexpires');
            $lockoutTime = $getAttribute('lockouttime');
            $logonCount = $getAttribute('logoncount');
            $badPwdCount = $getAttribute('badpwdcount');
            $lastLogoff = $getAttribute('lastlogoff');

            // Organizational Information
            $department = $safeEncode($getAttribute('department'));
            $title = $safeEncode($getAttribute('title'));
            $manager = $safeEncode($getAttribute('manager'));
            $officeLocation = $safeEncode($getAttribute('physicaldeliveryofficename'));
            $company = $safeEncode($getAttribute('company'));
            $division = $safeEncode($getAttribute('division'));
            $employeeId = $safeEncode($getAttribute('employeeid'));

            // Contact & Communication
            $telephoneNumber = $safeEncode($getAttribute('telephonenumber'));
            $mobile = $safeEncode($getAttribute('mobile'));
            $ipPhone = $safeEncode($getAttribute('ipphone'));
            $fax = $safeEncode($getAttribute('facsimiletelephonenumber'));
            $homePhone = $safeEncode($getAttribute('homephone'));
            $pager = $safeEncode($getAttribute('pager'));

            // Technical Information
            $userPrincipalName = $safeEncode($getAttribute('userprincipalname'));
            $objectSid = $getAttribute('objectsid');
            $homeDirectory = $safeEncode($getAttribute('homedirectory'));
            $profilePath = $safeEncode($getAttribute('profilepath'));
            $scriptPath = $safeEncode($getAttribute('scriptpath'));
            $primaryGroupId = $getAttribute('primarygroupid');
            $samAccountType = $getAttribute('samaccounttype');
            $userWorkstations = $safeEncode($getAttribute('userworkstations'));

            // Skip if no username (invalid user)
            if (!$username) {
                return null;
            }

            // Determine role based on group membership
            $role = $this->determineRole($memberOf);

            // Check if user exists in database
            $dbUser = LdapUser::where('username', $username)->first();
            $hasLoggedIn = $dbUser ? true : false;
            $lastLoginAt = $dbUser ? $dbUser->updated_at : null;

            return [
                'username' => $username,
                'display_name' => $displayName ?: $username,
                'email' => $email,
                'role' => $role,
                'distinguished_name' => $distinguishedName,
                'when_created' => $this->formatLdapDate($whenCreated),
                'last_logon' => $this->formatLdapDate($lastLogon),
                'pwd_last_set' => $this->formatLdapDate($pwdLastSet),
                'has_logged_in' => $hasLoggedIn,
                'last_login_at' => $lastLoginAt,
                'groups' => $this->extractGroupNames($memberOf),
                
                // Account Status & Security
                'account_status' => $this->getAccountStatus($userAccountControl, $accountExpires, $lockoutTime),
                'password_expires' => $this->formatLdapDate($pwdLastSet),
                'logon_count' => $logonCount ?: 0,
                'bad_password_count' => $badPwdCount ?: 0,
                'last_logoff' => $this->formatLdapDate($lastLogoff),
                
                // Organizational Information
                'department' => $department,
                'title' => $title,
                'manager' => $this->extractManagerName($manager),
                'office_location' => $officeLocation,
                'company' => $company,
                'division' => $division,
                'employee_id' => $employeeId,
                
                // Contact & Communication
                'telephone' => $telephoneNumber,
                'mobile' => $mobile,
                'ip_phone' => $ipPhone,
                'fax' => $fax,
                'home_phone' => $homePhone,
                'pager' => $pager,
                
                // Technical Information
                'user_principal_name' => $userPrincipalName,
                'object_sid' => $objectSid,
                'home_directory' => $homeDirectory,
                'profile_path' => $profilePath,
                'script_path' => $scriptPath,
                'primary_group_id' => $primaryGroupId,
                'account_type' => $this->getAccountType($samAccountType),
                'user_workstations' => $userWorkstations
            ];

        } catch (\Exception $e) {
            Log::error('Error formatting LDAP user data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Determine user role based on group membership
     */
    protected function determineRole($memberOf)
    {
        if (!$memberOf) {
            return 'user';
        }

        // Convert to array if it's not already
        $groups = is_array($memberOf) ? $memberOf : [$memberOf];

        foreach ($groups as $group) {
            // Extract group name from DN
            $groupName = $this->extractGroupNameFromDn($group);
            
            if (in_array($groupName, $this->adminGroups)) {
                return 'admin';
            }
        }

        return 'user';
    }

    /**
     * Extract group name from Distinguished Name
     */
    protected function extractGroupNameFromDn($dn)
    {
        // Extract CN from DN (e.g., "CN=Domain Admins,CN=Users,DC=ad,DC=mbmb,DC=gov,DC=my")
        if (preg_match('/CN=([^,]+)/', $dn, $matches)) {
            return $matches[1];
        }
        return $dn;
    }

    /**
     * Extract group names from memberOf attribute
     */
    protected function extractGroupNames($memberOf)
    {
        if (!$memberOf) {
            return [];
        }

        $groups = is_array($memberOf) ? $memberOf : [$memberOf];
        $groupNames = [];

        foreach ($groups as $group) {
            $groupName = $this->extractGroupNameFromDn($group);
            $groupNames[] = $groupName;
        }

        return $groupNames;
    }

    /**
     * Format LDAP date to readable format
     */
    protected function formatLdapDate($ldapDate)
    {
        if (!$ldapDate) {
            return null;
        }

        // If the value is an array, use the first element
        if (is_array($ldapDate)) {
            $ldapDate = $ldapDate[0] ?? null;
        }

        try {
            // LDAP dates are in format: 20231201120000.0Z
            $date = \DateTime::createFromFormat('YmdHis.0Z', $ldapDate);
            return $date ? $date->format('Y-m-d H:i:s') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get account status based on userAccountControl, accountExpires, and lockoutTime
     */
    protected function getAccountStatus($userAccountControl, $accountExpires, $lockoutTime)
    {
        if (!$userAccountControl) {
            return 'Unknown';
        }

        $uac = (int)$userAccountControl;
        
        // Check if account is disabled
        if ($uac & 0x0002) { // ACCOUNTDISABLE
            return 'Disabled';
        }
        
        // Check if account is locked
        if ($lockoutTime && $lockoutTime !== '0') {
            return 'Locked';
        }
        
        // Check if account has expired
        if ($accountExpires && $accountExpires !== '0' && $accountExpires !== '9223372036854775807') {
            $expiryDate = $this->formatLdapDate($accountExpires);
            if ($expiryDate && strtotime($expiryDate) < time()) {
                return 'Expired';
            }
        }
        
        return 'Active';
    }

    /**
     * Extract manager name from manager DN
     */
    protected function extractManagerName($manager)
    {
        if (!$manager) {
            return null;
        }

        // Extract CN from DN (e.g., "CN=John Doe,OU=Users,DC=domain,DC=com")
        if (preg_match('/CN=([^,]+)/', $manager, $matches)) {
            return $matches[1];
        }
        
        return $manager;
    }

    /**
     * Get account type based on samAccountType
     */
    protected function getAccountType($samAccountType)
    {
        if (!$samAccountType) {
            return 'User';
        }

        $type = (int)$samAccountType;
        
        switch ($type) {
            case 0x30000000: // NORMAL_ACCOUNT
                return 'User';
            case 0x10000000: // WORKSTATION_TRUST_ACCOUNT
                return 'Workstation';
            case 0x20000000: // INTERDOMAIN_TRUST_ACCOUNT
                return 'Interdomain Trust';
            case 0x40000000: // SERVER_TRUST_ACCOUNT
                return 'Server';
            case 0x80000000: // TEMP_DUPLICATE_ACCOUNT
                return 'Temporary';
            default:
                return 'Unknown';
        }
    }
} 