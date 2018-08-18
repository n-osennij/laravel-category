<?php

namespace nosennij\LaravelCategory;

use Illuminate\Support\ServiceProvider;

class LaravelCategoryServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nosennij');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {

            // Publishing database folder.
            $this->publishes([
                __DIR__ . '/../database' => database_path(),
            ], 'laravelcategory.db');

            // Publishing the configuration file.
            $this->publishes([
                __DIR__ . '/../config/laravelcategory.php' => config_path('laravelcategory.php'),
            ], 'laravelcategory.config');

            // Publishing the views.
            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/nosennij'),
            ], 'laravelcategory.views');

        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravelcategory.php', 'laravelcategory');

        // Register the service the package provides.
        $this->app->singleton('laravelcategory', function ($app) {
            return new LaravelCategory;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelcategory'];
    }
}