<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class LdapUser extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable;

    protected $table = 'ldap_users';

    protected $fillable = ['username', 'role'];

    protected $hidden = [
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the name attribute for the user.
     */
    public function getNameAttribute()
    {
        return $this->username;
    }

    /**
     * Get the email attribute for the user.
     */
    public function getEmailAttribute()
    {
        return $this->username . '@' . config('ldap.domain', 'local.com');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }
}
