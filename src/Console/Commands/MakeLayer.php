<?php

namespace WilliamJSS\Layers\Console\Commands;

use Illuminate\Console\Command;

class MakeLayer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        layers {name}
        {--e|eloquent : Generate a repository eloquent for the model}
        {--i|interface : Generate a repository interface for the model}
        {--s|service : Generate a service for the model}
        {--r|repository : Generate a repository interface and eloquent for the model}
        {--a|all : Generate a service, repository interface and repository eloquent for the model}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new class from layered architecture';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $options = $this->options();

        if ($options['all']) {
            $this->call('layers:repository', ['name' => $name, '--interface' => true]);
            $this->call('layers:repository', ['name' => $name, '--eloquent' => true]);
            $this->call('layers:service', ['name' => $name]);

            return Command::SUCCESS;
        }

        if ($options['repository']) {
            $this->call('layers:repository', ['name' => $name, '--interface' => true]);
            $this->call('layers:repository', ['name' => $name, '--eloquent' => true]);

            return Command::SUCCESS;
        }

        if ($options['interface']) {
            $this->call('layers:repository', ['name' => $name, '--eloquent' => true]);
        }

        if ($options['eloquent']) {
            $this->call('layers:repository', ['name' => $name, '--interface' => true]);
        }

        if ($options['service']) {
            $this->call('layers:service', ['name' => $name]);
        }

        return Command::SUCCESS;
    }

}

