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
    | BearyChat Clients
    |--------------------------------------------------------------------------
    |
    | Here are each of the BearyChat clients setup for your application.
    |
    | Supported keys:
    |
    |   'webhook':      The Incoming Webhook URL. You can get it from an Incoming Robot.
    |                   See https://bearychat.kf5.com/posts/view/26755/
    |   'message_defaults': Optional message defaults. All keys of message defaults
    |                       are listed in `ElfSundae\BearyChat\MessageDefaults`.
    |                       Supported: "channel", "user", "markdown" (boolean),
    |                       "notification", "attachment_color".
    |
    */

    'clients' => [

        'default' => [
            'webhook' => '',
            // 'message_defaults' => [
            //     'attachment_color' => '#f5f5f5',
            // ],
        ],

        // 'admin' => [
        //     'webhook' => '',
        // ],

    ],

];
