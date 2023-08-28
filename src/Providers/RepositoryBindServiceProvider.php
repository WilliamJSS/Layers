<?php

namespace WilliamJSS\Layers\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class RepositoryBindServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $path = config('layers.path.repositories');
        
        if (File::exists($path)) {

            # Search files in repository folder
            $merge = collect();
            for ($i = 0; $i <= 2; $i++) {
                $folders = collect((new Finder)->files()->depth($i)->in($path))
                ->map(fn($file) => $file->getBasename('.php'))
                ->collect()
                ->all();
                
                $merge = $merge->merge($folders);
            }
            
            # Save only repository subfolder and model into array
            $models = $merge->keys()->collect()->map(function ($file, $path) {
                $model = str_replace($path . '/', '', $file);
                $model = str_replace('.php', '', $model);
                if (Str::contains($model, 'Interface')) {
                    return str_replace('Interface', '', $model);
                }
            })->values()->all();

            # Bind repositories interfaces/eloquents
            $namespace = config('layers.namespace.repositories');
            foreach ($models as $model) {
                if ($model != null) {
                    $this->app->bind(
                        str_replace('/', '\\', Str::ucfirst($namespace) . '/' . $model) . 'Interface',
                        str_replace('/', '\\', Str::ucfirst($namespace) . '/' . $model) . 'Eloquent'
                    );
                }
            }
        }
    }
}