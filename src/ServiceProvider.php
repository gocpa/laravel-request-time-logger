<?php

declare(strict_types=1);

namespace GoCPA\LaravelRequestTimeLogger;

use GoCPA\LaravelRequestTimeLogger\Console\AboutCommandIntegration;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(Kernel $kernel): void
    {
        $configFileName = 'gocpa-laravel-request-time-logger.php';
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/'.$configFileName => config_path($configFileName),
            ], 'config');

            $this->registerAboutCommandIntegration();
        }

        $this->mergeConfigFrom(__DIR__.'/../config/'.$configFileName, 'config');
    }

    /**
     * Register the `php artisan about` command integration.
     */
    protected function registerAboutCommandIntegration(): void
    {
        // The about command is only available in Laravel 9 and up so we need to check if it's available to us
        if (!class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('gocpa/laravel-request-time-logger', fn () => ['Version' => '1.0.0']);
    }
}
