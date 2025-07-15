# LDAP Authentication Setup Guide

This guide explains how to set up LDAP authentication with Active Directory for your Laravel application.

## Prerequisites

- Laravel application with Fortify installed
- Active Directory server accessible from your application
- LDAPRecord package installed (`directorytree/ldaprecord-laravel`)

## Configuration

### 1. Environment Variables

Add the following LDAP configuration to your `.env` file:

```env
# LDAP Configuration for Active Directory
LDAP_CONNECTION=default
LDAP_HOST=192.168.1.100
LDAP_USERNAME=cn=admin,dc=example,dc=com
LDAP_PASSWORD=admin_password
LDAP_PORT=389
LDAP_BASE_DN=dc=example,dc=com
LDAP_TIMEOUT=5
LDAP_SSL=false
LDAP_TLS=false
LDAP_SASL=false
LDAP_LOGGING=true
LDAP_CACHE=false
LDAP_DOMAIN=example.com
```

### 2. Configuration Details

- `LDAP_HOST`: Your Active Directory server IP or hostname
- `LDAP_USERNAME`: Service account DN for LDAP queries (e.g., `cn=admin,dc=example,dc=com`)
- `LDAP_PASSWORD`: Service account password
- `LDAP_BASE_DN`: Base distinguished name for your domain (e.g., `dc=example,dc=com`)
- `LDAP_PORT`: LDAP port (389 for standard, 636 for SSL)
- `LDAP_SSL`: Set to `true` if using SSL connection
- `LDAP_TLS`: Set to `true` if using TLS connection
- `LDAP_DOMAIN`: Your Active Directory domain name

### 3. Admin Groups Configuration

The system automatically assigns admin roles to users who are members of specific Active Directory groups. You can configure these groups in `config/ldap.php`:

```php
'admin_groups' => [
    'Domain Admins',
    'Enterprise Admins',
    'Schema Admins',
    'Administrators',
],
```

## Usage

### 1. Access LDAP Login

Navigate to `/login-ldap` to access the LDAP authentication form.

### 2. User Authentication Flow

1. User enters their Active Directory username and password
2. System validates credentials against Active Directory
3. If valid, user is created/updated in the local `ldap_users` table
4. User role is determined based on Active Directory group membership
5. User is logged in and redirected based on their role

### 3. Role-Based Access

- **Admin users**: Redirected to `/admin/home`
- **Regular users**: Redirected to `/home`

## Database Schema

The `ldap_users` table stores:

- `id`: Primary key
- `username`: Active Directory username
- `role`: User role ('admin' or 'user')
- `created_at`: Record creation timestamp
- `updated_at`: Record update timestamp

## Security Considerations

1. **Service Account**: Use a dedicated service account with minimal privileges
2. **SSL/TLS**: Enable SSL or TLS for production environments
3. **Password Policy**: Ensure strong passwords for the service account
4. **Network Security**: Restrict LDAP access to trusted networks
5. **Logging**: Enable LDAP logging for debugging and monitoring

## Troubleshooting

### Common Issues

1. **Connection Failed**
   - Verify LDAP server is accessible
   - Check firewall settings
   - Validate hostname/IP address

2. **Authentication Failed**
   - Verify service account credentials
   - Check base DN configuration
   - Ensure user exists in Active Directory

3. **Group Membership Issues**
   - Verify admin group names match Active Directory
   - Check user group membership
   - Review LDAP query permissions

### Debug Mode

Enable LDAP logging by setting `LDAP_LOGGING=true` in your `.env` file. Check the Laravel logs for detailed error messages.

## Testing

1. Create a test user in Active Directory
2. Add the user to an admin group if testing admin functionality
3. Try logging in with the test credentials
4. Verify the user is created in the `ldap_users` table
5. Check that the correct role is assigned

## Support

For issues related to:
- **LDAPRecord package**: Check the [official documentation](https://ldaprecord.com/)
- **Active Directory**: Consult your system administrator
- **Laravel application**: Check Laravel logs and documentation 