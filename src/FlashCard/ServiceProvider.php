<?php

namespace Moo\FlashCard;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Moo\FlashCard\Command\CreateCard;
use Moo\FlashCard\Command\CreateCategory;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load database migration classes
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Load command line tool for add cards or categories
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateCard::class,
                CreateCategory::class,
            ]);
        }

        // Load API routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
