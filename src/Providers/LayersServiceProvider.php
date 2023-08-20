<?php

namespace WilliamJSS\Layers\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use WilliamJSS\Layers\Console\Commands\MakeLayer;
use WilliamJSS\Layers\Console\Commands\MakeRepository;
use WilliamJSS\Layers\Console\Commands\MakeService;

class LayersServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->register('WilliamJSS\\Layers\\Providers\\RepositoryBindServiceProvider');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeLayer::class,
                MakeRepository::class,
                MakeService::class,
            ]);

            // Create repository directory
            $path = app_path('Repositories');
            if (!File::exists($path)) {
                File::makeDirectory($path);
            }
        }
    }
}