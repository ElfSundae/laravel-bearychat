<?php

if (! function_exists('bearychat'))
{
    function bearychat($name = 'default')
    {
        return app('bearychat')->client($name);
    }
}
