<?php

namespace Modernben\LaravelLiveDatatables\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class LaravelLiveDatatablesMakeCommand extends Command
{
    public $signature = 'make:livetable
                        {name : The name of the model}
                        {--force : Force create the files}';

    public $description = 'Create a new Livewire DB view-driven datatable';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->createModel();
        $this->createMigration();
        $this->createLivewireComponent();

        $this->comment('Next Steps:');
        $this->comment('1. Edit the migration file with your custom view query.');
        $this->comment('2. php artisan migrate');
        $this->comment('3. $$ Profit');
    }

    public function createModel()
    {
        $args = [
            'name' => $this->argument('name'),
        ];

        if ($this->option('force')) {
            $args['--force'] = true;
        }

        $this->call('make:livetable-model', $args);
    }

    public function createMigration()
    {
        $name = $this->argument('name');
        $class = 'Datatable' . Str::studly(class_basename($name));
        $view = 'datatable_' . Str::snake(Str::studly(class_basename($name)));
        $path = $this->getMigrationPath();
        $file = '_create_' . $view . '_view.php';
        $filename = date('Y_m_d_His') . '_create_' . $view . '_view.php';

        if (!$this->option('force')) {
            $this->ensureMigrationDoesntAlreadyExist($file);
        }

        $stub = __DIR__ . '/stubs/migration.stub';
        $stub = $this->files->get($stub);

        $this->files->put(
            $path . '/' . $filename,
            $this->populateStub($class, $stub, $view)
        );

        $this->info('Migration created. ' . $filename);
    }

    public function createLivewireComponent()
    {
        $name = $this->argument('name');
        $studly = Str::studly($name);

        $stub = __DIR__ . '/stubs/livewire_component.stub';
        $stub = $this->files->get($stub);


        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $studly, $stub
        );

        $stub = str_replace(
            ['{{ filename }}'],
            Str::kebab($name) . '-datatable', $stub
        );

        $this->files->put(app_path('Http/Livewire/' . $studly . 'Datatable.php'), $stub);

        $this->info('Livewire Component created. ' . app_path('Http/Livewire/' . $studly . '.php'));


        $stub = __DIR__ . '/stubs/livewire_view.stub';
        $stub = $this->files->get($stub);

        $this->files->put(resource_path('views/livewire/' . Str::kebab($name) . '-datatable.blade.php'), $stub);

        $this->info('Livewire View created. ' . resource_path('views/livewire/' . Str::kebab($name) . '-datatable.blade.php'));
    }


    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            Str::studly($name), $stub
        );

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getMigrationPath()
    {
        return database_path('migrations');
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($file)
    {
        $migrationPath = $this->getMigrationPath();

        $migrationFiles = $this->files->glob($migrationPath.'/*'.$file);

        if (! empty($migrationFiles)) {
            throw new InvalidArgumentException("Migration already exists.");
        }
    }

     /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

}
