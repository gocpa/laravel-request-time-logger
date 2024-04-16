<?php

declare(strict_types=1);

namespace GoCPA\LaravelRequestTimeLogger;

use Illuminate\Contracts\Http\Kernel;
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
        }

        $this->mergeConfigFrom(__DIR__.'/../config/'.$configFileName, 'config');
    }
}
