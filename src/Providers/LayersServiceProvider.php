<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class LayersServiceProvider extends ServiceProvider
{
    public function register()
    {
        # Busca os arquivos na pasta de Repositories
        $merge = collect();
        for ($i = 0; $i <= 2; $i++) {
            $folders = collect((new Finder)->files()->depth($i)->in(app_path('Repositories')))
                ->map(fn($file) => $file->getBasename('.php'))
                ->collect()
                ->all();

            $merge = $merge->merge($folders);
        }

        # Salva apenas a subpasta e o model do Repository em um array
        $models = $merge->keys()->collect()->map(function ($file) {
            $model = str_replace(app_path('Repositories') . '/', '', $file);
            $model = str_replace('.php', '', $model);
            if (Str::contains($model, 'Interface')) {
                return str_replace('Interface', '', $model);
            }
        })->values()->all();

        # Efetua os binds das interfaces/eloquents
        foreach ($models as $model) {
            if ($model != null) {
                $this->app->bind(
                    'App\\Repositories\\' . str_replace('/', '\\', $model) . 'Interface',
                    'App\\Repositories\\' . str_replace('/', '\\', $model) . 'Eloquent'
                );
            }
        }

    }

    public function boot()
    {

    }
}