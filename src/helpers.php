<?php

if (! function_exists('bearychat')) {
    /**
     * Get the BearyChat client instance.
     *
     * @param  string  $name
     * @return \ElfSundae\BearyChat\Client
     */
    function bearychat($name = null)
    {
        return app('bearychat')->client($name);
    }
}
