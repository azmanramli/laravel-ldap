<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default LDAP Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the LDAP connections below you wish
    | to use as your default connection for all LDAP operations. Of
    | course you may add as many connections you'd like below.
    |
    */

    'default' => env('LDAP_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    |
    | Below you may configure each LDAP connection your application requires
    | access to. Be sure to include a valid base DN - otherwise you may
    | not receive any results when performing LDAP search operations.
    |
    */

'connections' => [

    'default' => [
        'hosts' => [env('LDAP_HOST', '172.17.10.19')],
        'username' => env('LDAP_USERNAME', 'cn=Administrator,cn=Users,dc=ad,dc=mbmb,dc=gov,dc=my'),
        'password' => env('LDAP_PASSWORD', 'Rootjalur99'),
        'port' => env('LDAP_PORT', 389),
        'base_dn' => env('LDAP_BASE_DN', 'dc=ad,dc=mbmb,dc=gov,dc=my'), // 🔄 Changed from OU=Users to domain root
        'timeout' => env('LDAP_TIMEOUT', 10),
        'use_ssl' => env('LDAP_SSL', false),
        'use_tls' => env('LDAP_TLS', false),
        'use_sasl' => env('LDAP_SASL', false),
        'sasl_options' => [],
    ],

],




    /*
    |--------------------------------------------------------------------------
    | LDAP Logging
    |--------------------------------------------------------------------------
    |
    | When LDAP logging is enabled, all LDAP search and authentication
    | operations are logged using the default application logging
    | driver. This can assist in debugging issues and more.
    |
    */

    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'channel' => env('LOG_CHANNEL', 'stack'),
        'level' => env('LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Cache
    |--------------------------------------------------------------------------
    |
    | LDAP caching enables the ability of caching search results using the
    | query builder. This is great for running expensive operations that
    | may take many seconds to complete, such as a pagination request.
    |
    */

    'cache' => [
        'enabled' => env('LDAP_CACHE', false),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LDAP Admin Groups
    |--------------------------------------------------------------------------
    |
    | Define the Active Directory groups that should be considered as admin
    | groups. Users who are members of these groups will be assigned the
    | 'admin' role in the application.
    |
    */

    'admin_groups' => [
        'Domain Admins',
        'Enterprise Admins',
        'Schema Admins',
        'Administrators',
        'Scientists',  // Forumsys.com might have this group
        'Mathematicians', // Forumsys.com might have this group
    ],

    // Username attribute for LDAP search (e.g., 'uid' for OpenLDAP, 'samaccountname' for AD)
    'username_attribute' => env('LDAP_USERNAME_ATTRIBUTE', 'samaccountname'),

];
