<?php

return [
    'enable'          => env('SSO_ENABLE', true),
    'base_address'    => env('SSO_BASE_ADDRESS'),
    'client_id'       => env('SSO_CLIENT_ID'),
    'client_secret'   => env('SSO_CLIENT_SECRET'),
    'auth_middleware' => env('SSO_AUTH_GUARD', 'auth:sanctum')
];
