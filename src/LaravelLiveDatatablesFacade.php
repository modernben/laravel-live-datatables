<?php

namespace Modernben\LaravelLiveDatatables;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Modernben\LaravelLiveDatatables\LaravelLiveDatatables
 */
class LaravelLiveDatatablesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-live-datatables';
    }
}
