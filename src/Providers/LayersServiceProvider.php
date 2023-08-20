<?php

namespace WilliamJSS\Layers\Providers;

use Illuminate\Support\ServiceProvider;
use WilliamJSS\Layers\Console\Commands\MakeLayer;
use WilliamJSS\Layers\Console\Commands\MakeRepository;
use WilliamJSS\Layers\Console\Commands\MakeService;

class LayersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeLayer::class,
                MakeRepository::class,
                MakeService::class,
            ]);
        }
    }
}