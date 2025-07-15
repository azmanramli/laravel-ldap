<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\Commands\FortifyUITablerCommand;
use App\Providers\Commands\FortifyUITablerUpdateCommand;


class FortifyUITablerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Load Routes
            $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
            // Load Migrations
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

            // Publish files
            $this->publishes([
                __DIR__ . '/../../resources/views' => base_path('resources/views'),
                __DIR__ . '/../../public' => base_path('public'),
                __DIR__ . '/../../resources/lang' => base_path('resources/lang'),
                __DIR__ . '/app/Http/Controllers' => base_path('app/Http/Controllers'),
            ], 'tabler-resources');

            // Update all files
            $this->publishes([
                __DIR__ . '/../../resources/views' => base_path('resources/views'),
                __DIR__ . '/../../public' => base_path('public'),
                __DIR__ . '/../../resources/lang' => base_path('resources/lang'),
                __DIR__ . '/../../app/Http/Controllers' => base_path('app/Http/Controllers'),
            ], 'tabler-update-full');

            // Update public files
            $this->publishes([
                __DIR__ . '/../../public' => base_path('public'),
            ], 'tabler-update-public');

            // Update views
            $this->publishes([
                __DIR__ . '/../../resources/views' => base_path('resources/views'),
            ], 'tabler-update-views');

            // Update controllers
            $this->publishes([
                __DIR__ . '/../../app/Http/Controllers' => base_path('app/Http/Controllers'),
            ], 'tabler-update-controllers');

            // Update all files
            $this->publishes([
                __DIR__ . '/../../resources/lang' => base_path('resources/lang'),
            ], 'tabler-update-language');

            $this->commands([
                FortifyUITablerCommand::class,
                FortifyUITablerUpdateCommand::class,
            ]);
        }
    }
}
