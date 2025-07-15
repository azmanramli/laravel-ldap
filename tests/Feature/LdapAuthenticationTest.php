<?php

namespace Tests\Feature;

use App\Models\LdapUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LdapAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_login_requires_username_and_password()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['username', 'password']);
    }

    public function test_ldap_user_can_be_created_in_database()
    {
        $userData = [
            'username' => 'testuser',
            'role' => 'user',
        ];

        $user = LdapUser::create($userData);

        $this->assertDatabaseHas('ldap_users', $userData);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals('user', $user->role);
    }

    public function test_ldap_user_can_be_admin()
    {
        $userData = [
            'username' => 'adminuser',
            'role' => 'admin',
        ];

        $user = LdapUser::create($userData);

        $this->assertTrue($user->isAdmin());
        $this->assertEquals('admin', $user->role);
    }

    public function test_ldap_user_has_email_attribute()
    {
        $user = LdapUser::create([
            'username' => 'testuser',
            'role' => 'user',
        ]);

        $this->assertEquals('testuser@local.com', $user->email);
    }

    public function test_ldap_user_has_name_attribute()
    {
        $user = LdapUser::create([
            'username' => 'testuser',
            'role' => 'user',
        ]);

        $this->assertEquals('testuser', $user->name);
    }
} 