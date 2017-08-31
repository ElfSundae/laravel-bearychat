<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default BearyChat Client Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the BearyChat clients below you wish to use
    | as your default client.
    |
    */

    'default' => env('BEARYCHAT_CLIENT', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Clients Defaults
    |--------------------------------------------------------------------------
    |
    | Here you may specify some defaults for all clients.
    |
    | Supported keys: 'webhook', 'message_defaults'.
    |
    | All possible keys for 'message_defaults' are listed in
    | `ElfSundae\BearyChat\MessageDefaults`:
    | "channel", "user", "markdown" (boolean), "notification", "attachment_color".
    |
    */

    'clients_defaults' => [
        // 'webhook' => 'https://hook.bearychat.com/=',
    ],

    /*
    |--------------------------------------------------------------------------
    | BearyChat Clients
    |--------------------------------------------------------------------------
    |
    | Here are each of the BearyChat clients setup for your application.
    |
    */

    'clients' => [
        'default' => [
            'webhook' => 'https://hook.bearychat.com/=',
            'message_defaults' => [
                'channel' => 'all',
            ],
        ],
    ],

];
