<?php

namespace WilliamJSS\Layers\Providers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class RepositoryBindServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Create repository directory
        $path = app_path('Repositories');
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        if (Storage::exists($path)) {
            
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
            $models = $merge->keys()->collect()->map(function ($file) {
                $model = str_replace(app_path('Repositories') . '/', '', $file);
                $model = str_replace('.php', '', $model);
                if (Str::contains($model, 'Interface')) {
                    return str_replace('Interface', '', $model);
                }
            })->values()->all();

            # Bind repositories interfaces/eloquents
            foreach ($models as $model) {
                if ($model != null) {
                    $this->app->bind(
                        'App\\Repositories\\' . str_replace('/', '\\', $model) . 'Interface',
                        'App\\Repositories\\' . str_replace('/', '\\', $model) . 'Eloquent'
                    );
                }
            }
        }
    }
}