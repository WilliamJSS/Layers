<?php

namespace WilliamJSS\Layers\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;

class MakeService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'layers:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service file';

    protected $type = 'Service file';

    protected function getNameInput()
    {
        return str_replace('.', '/', trim($this->argument('name')));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('vendor/williamjss/layers') . '/src/Console/Commands/Stubs/Service.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . 'Service.php';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $name;

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $name = $this->argument('name');

        $namespaceRepo = $this->parseModel($name);

        $replace = [
            '{{ namespaceRepository }}' => $namespaceRepo,
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Qualify the given model class base name.
     *
     * @param  string  $model
     * @return string
     */
    protected function qualifyModel(string $model)
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model) . 'RepositoryInterface';

        $rootNamespace = $this->rootNamespace();

        foreach ($this->possiblesRepositories() as $key => $value) {
            if (Str::contains($value, $model)) {
                return $rootNamespace . 'Repositories\\' . str_replace('/', '\\', $value);
            }
        }

        print_r($this->possiblesRepositories());

        return $rootNamespace . 'Repositories\\' . $model;
    }

    /**
     * Get a possibles repositories namespace
     *
     * @return array<int, string>
     */
    protected function possiblesRepositories()
    {
        $repoPath = app_path('Repositories');

        $merge = collect();
        $depth = 2;

        for ($i = 0; $i <= $depth; $i++) {
            $folders = collect((new Finder)->files()->depth($i)->in($repoPath))
                ->map(fn($file) => $file->getBasename('.php'))
                ->collect();

            $merge = $merge->merge($folders);
        }

        return $merge
            ->keys()
            ->collect()
            ->map(function ($file) {
                $model = str_replace(app_path('Repositories') . '/', '', $file);
                $model = str_replace('.php', '', $model);
                return $model;
            })
            ->collect()
            ->all();
    }
}