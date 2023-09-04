<?php

namespace WilliamJSS\Layers\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ListBinds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layers:binds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all binds from application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
            $models = $merge->keys()->collect()->map(function ($file) {
                $model = str_replace('.php', '', $file);
                $model = str_replace(base_path() . '/', '', $model);
                if (Str::contains($model, 'Interface')) {
                    return str_replace('Interface', '', $model);
                }
            })->values()->all();
            
            # Bind repositories interfaces/eloquents
            foreach ($models as $model) {
                if ($model != null) {
                    echo(str_replace('/', '\\', Str::ucfirst($model)) . 'Interface' . "\n");
                    echo(str_replace('/', '\\', Str::ucfirst($model)) . 'Eloquent' . "\n\n");
                }
            }
        }
    }

}
