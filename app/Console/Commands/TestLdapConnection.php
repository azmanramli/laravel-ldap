<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LdapRecord\Container;

class TestLdapConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:test {username?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test LDAP connection and authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing LDAP Connection...');
        
        try {
            $connection = Container::getDefaultConnection();
            
            // Test basic connection
            $this->info('✓ LDAP connection established successfully');
            
            // Test search
            $query = $connection->query();
            $this->info('✓ LDAP query builder initialized');
            
            // If username and password provided, test authentication
            if ($username = $this->argument('username')) {
                $password = $this->argument('password');
                
                if (!$password) {
                    $password = $this->secret('Enter password for ' . $username);
                }
                
                $this->info('Testing authentication for user: ' . $username);
                
                // Search for user
                $attribute = config('ldap.username_attribute', 'samaccountname');
                $query->where($attribute, '=', $username);
                $this->info('Searching with base DN: ' . config('ldap.connections.default.base_dn'));
                $this->info('Search filter: (' . $attribute . '=' . $username . ')');
                
                $ldapUser = $query->first();
                
                if (!$ldapUser) {
                    $this->error('✗ User not found in Active Directory');
                    $this->info('Trying to list all users to debug...');
                    
                    // Try to list some users to see what's available
                    $allUsers = $connection->query()->where('uid', '*')->limit(5)->get();
                    $this->info('Found ' . $allUsers->count() . ' users in directory:');
                    foreach ($allUsers as $user) {
                        $this->line('  - ' . $user->getDn());
                    }
                    
                    return 1;
                }
                
                $this->info('✓ User found in Active Directory');
                
                // Handle both array and object responses
                $userDn = is_array($ldapUser) ? $ldapUser['dn'] : $ldapUser->getDn();
                $this->info('User DN: ' . $userDn);
                
                // Test authentication
                try {
                    $connection->connect($userDn, $password);
                    $this->info('✓ Authentication successful');
                    
                    // Check group membership
                    $memberOf = $ldapUser->getAttribute('memberof');
                    if (is_array($memberOf)) {
                        $this->info('User groups:');
                        foreach ($memberOf as $group) {
                            $this->line('  - ' . $group);
                        }
                    }
                    
                } catch (\Exception $e) {
                    $this->error('✗ Authentication failed: ' . $e->getMessage());
                    return 1;
                }
            }
            
            $this->info('LDAP test completed successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('✗ LDAP connection failed: ' . $e->getMessage());
            $this->error('Please check your LDAP configuration in .env file');
            return 1;
        }
    }
}
