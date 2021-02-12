<?php

namespace Modernben\LaravelLiveDatatables\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modernben\LaravelLiveDatatables\LaravelLiveDatatablesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Modernben\\LaravelLiveDatatables\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelLiveDatatablesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_laravel_live_datatables_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
