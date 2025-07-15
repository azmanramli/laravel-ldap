# Laravel 11 + Fortify + FortifyUI + FortifyUI Tabler

## About

Perhaps this should be done differently. But this works for me now. With Laravel 11, things are a bit different and FortifyUI is not working correctly out of the box.

I need a boilerplate for Laravel with the Tabler Admin template. So this is it.

## Requirement

* PHP >= 8.2, as required by Laravel 11

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

4. Update the ```.env``` file accordingly. Consider which database you are using.

5. Run the following command in the Terminal.
```
php artisan migrate
```
```
php artisan key:generate
```
```
php artisan storage:link
```

6. Browse to your localhost or your web app address.
