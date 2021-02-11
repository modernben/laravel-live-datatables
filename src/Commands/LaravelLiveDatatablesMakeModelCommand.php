<?php

namespace Modernben\LaravelLiveDatatables\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class LaravelLiveDatatablesMakeModelCommand extends GeneratorCommand
{
    public $name = 'make:livetable-model';

    public $description = 'Create all the necessary files for a view-driven datatable';

    public $type = 'Model';

    public $hidden = true;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Livewire\Datatable';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the mailable already exists'],
        ];
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        $stub = str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $stub);
        $stub = str_replace(['SnakeDummyClass', '{{ snake_class }}', '{{snake_class}}'], Str::snake($class), $stub);

        return $stub;
    }
}
