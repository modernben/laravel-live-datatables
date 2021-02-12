<?php

namespace Modernben\LaravelLiveDatatables;

use Modernben\LaravelLiveDatatables\Commands\LaravelLiveDatatablesMakeCommand;
use Modernben\LaravelLiveDatatables\Commands\LaravelLiveDatatablesMakeModelCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelLiveDatatablesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-live-datatables')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_live_datatables_table')
            ->hasCommands([
                LaravelLiveDatatablesMakeCommand::class,
                LaravelLiveDatatablesMakeModelCommand::class,
            ]);
    }
}
