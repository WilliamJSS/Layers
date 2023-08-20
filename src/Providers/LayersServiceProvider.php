<?php

namespace WilliamJSS\Layers\Providers;

use Illuminate\Support\ServiceProvider;
use WilliamJSS\Layers\Console\Commands\MakeLayer;
use WilliamJSS\Layers\Console\Commands\MakeRepositoryEloquent;
use WilliamJSS\Layers\Console\Commands\MakeRepositoryInterface;
use WilliamJSS\Layers\Console\Commands\MakeService;

class LayersServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register('WilliamJSS\Layers\Providers\RepositoryBindServiceProvider');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeLayer::class,
                MakeRepositoryEloquent::class,
                MakeRepositoryInterface::class,
                MakeService::class,
            ]);
        }
    }
}