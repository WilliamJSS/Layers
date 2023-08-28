<?php

namespace WilliamJSS\Layers\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
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
    protected $signature = '
        layers:service {name}
        {--wr=*}
    ';

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
        $stubs_path = base_path('vendor/williamjss/layers') . '/src/Console/Commands/Stubs/';
        
        if ($this->withRepositories()) {
            return $stubs_path . 'ServiceMultiRepositories.stub';
        } else {
            return $stubs_path . 'Service.stub';
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\' . config('layers.namespace.services');
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
        if ($this->options()['wr']){
            $repositories = '';
            $variables = '';
            $construct = '';
            $thisConstruct = '';
            foreach ($this->options()['wr'] as $key => $value) {
                $repositories = Str::of($repositories)->newLine()->append('use ' . $this->qualifyModel($value) . ';');
                $variables = Str::of($variables)->newLine()->append('    private $repo' . $value . ';');
                $construct = Str::of($construct)->newLine()->append('        ' . $value . 'RepositoryInterface $repo' . $value . ',');
                $thisConstruct = Str::of($thisConstruct)->newLine()->append('        $this->repo' . $value . ' = $repo' . $value . ';');
            }
            $replace = [
                '{{ repositories }}' => $repositories,
                '{{ variables }}' => $variables,
                '{{ construct }}' => $construct,
                '{{ thisConstruct }}' => $thisConstruct,
            ];
        } else {
            $namespaceRepo = $this->parseModel($model);
            $replace = [
                '{{ namespaceRepository }}' => $namespaceRepo,
            ];
        }

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
        $model = class_basename($model) . 'RepositoryInterface';

        $repo_path = config('layers.path.repositories');
        $repo_namespace = config('layers.namespace.repositories');

        foreach ($this->possiblesRepositories() as $key => $value) {
            if (Str::contains($value, $model)) {
                return $this->rootNamespace() . str_replace('/', '\\', Str::replaceFirst($repo_path, $repo_namespace, $value));
            }
        }

        throw new InvalidArgumentException('Repository not found: ' . $model);
    }

    /**
     * Get a possibles repositories namespace
     *
     * @return array<int, string>
     */
    protected function possiblesRepositories()
    {
        $repoPath = config('layers.path.repositories');

        if(!File::exists($repoPath)){
            throw new InvalidArgumentException('Invalid repository path: ' . $repoPath);
        }

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
            ->map(function ($file, $repoPath) {
                $model = str_replace($repoPath . '/', '', $file);
                $model = str_replace('.php', '', $model);
                return $model;
            })
            ->collect()
            ->all();
    }

    /**
     * Verify 'with-repositories' option
     *
     * @return bool
     */
    protected function withRepositories(){
        if($this->options()['wr']){
            return TRUE;
        } else {
            return FALSE;
        }
    }
}