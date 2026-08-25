<?php

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // servidor de desarrollo de Angular
    'allowed_origins' => ['http://localhost:4200'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
