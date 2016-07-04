<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BearyChat Clients
    |--------------------------------------------------------------------------
    |
    | Here you may define BearyChat clients for your application.
    |
    */

    'default' => [

        /**
         * BearyChat Incoming Webhook URL.
         *
         * You can get the webhook URL from an Incoming Robot.
         * https://bearychat.kf5.com/posts/view/26755/
         */
        'webhook' => '',

        /**
         * (Optional) BearyChat Message Defaults.
         *
         * All keys of message defaults are listed in ElfSundae\BearyChat\MessageDefaults.
         *
         * Supported: "channel", "user", "markdown" (boolean), "notification", "attachment_color".
         */
        'message_defaults' => [
            'markdown' => true,
        ]

    ],

];
