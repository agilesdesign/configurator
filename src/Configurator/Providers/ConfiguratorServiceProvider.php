<?php

namespace Configurator\Providers;

use Configurator\Foundation\Repository;
use Illuminate\Support\ServiceProvider;

class ConfiguratorServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->replaceLaravelConfigRepository();
    }

    protected function replaceLaravelConfigRepository()
    {
        $items = app('config')->all();

        $this->app->singleton('config', function () use ($items) {
            return new Repository($items);
        });
    }
}
