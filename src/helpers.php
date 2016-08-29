<?php

if (! function_exists('bearychat')) {
    function bearychat($name = null)
    {
        return app('bearychat')->client($name);
    }
}
