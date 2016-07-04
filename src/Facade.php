<?php

namespace ElfSundae\BearyChat\Laravel;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * @see \ElfSundae\BearyChat\Laravel\ClientManager
 */
class Facade extends LaravelFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bearychat';
    }
}
