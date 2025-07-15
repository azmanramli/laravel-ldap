<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LdapService;

class TestLdapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:test-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test LDAP connection and fetch all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing LDAP connection...');
        
        try {
            $service = new LdapService();
            $users = $service->getAllUsers();
            
            $this->info('Success! Found ' . count($users) . ' users in Active Directory.');
            
            if (count($users) > 0) {
                $this->info('First 5 users:');
                $this->table(
                    ['Username', 'Display Name', 'Email', 'Role', 'Has Logged In', 'Groups'],
                    array_map(function($user) {
                        $getScalar = function($value) {
                            if (is_array($value)) {
                                return implode(', ', $value);
                            }
                            return $value ?? '';
                        };
                        return [
                            $getScalar($user['username'] ?? ''),
                            $getScalar($user['display_name'] ?? ''),
                            $getScalar($user['email'] ?? ''),
                            $getScalar($user['role'] ?? ''),
                            $user['has_logged_in'] ? 'Yes' : 'No',
                            $getScalar($user['groups'] ?? [])
                        ];
                    }, array_slice($users, 0, 5))
                );
            }
            
        } catch (\Exception $e) {
            $this->error('LDAP Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
