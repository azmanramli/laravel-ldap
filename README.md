# Laravel 11 + FortifyUI + FortifyUI Tabler + LDAP

## About

With Laravel 11, some things have changed — and while FortifyUI isn’t working perfectly out of the box, this setup works well for me at the moment.

This is a Laravel boilerplate project that integrates the Tabler Admin Template for a clean and modern UI.

It’s already configured with the required connection details (provided by me), so it’s ready to run.

By default, new users are assigned the "user" role. You can easily update roles directly from the database or customize it further to fit your preference.

## Requirement

* PHP >= 8.2 + LDAP extension is ENABLE, as required by Laravel 11

## How to Install

1. Get the source for this project, either you download it via ZIP or Clone it to your local computer.

2. Run the following command in the Terminal.
```
composer install
```

3. Copy and create a new .env file with the following command
```
copy .env.example .env
```

4. Update the ```.env``` file accordingly. Consider which database you are using and ldap connection given.
   
5. Update the config/ldap.php 'connections' => using connection given.

6. Run the following command in the Terminal.
```
php artisan migrate
```
```
php artisan key:generate
```
```
php artisan storage:link
```

6. Browse to your localhost or your web app address and login using your LDAP username and password.
