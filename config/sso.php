<?php

return [
    'enable'      => env('SSO_ENABLE', true),
    'server'      => [
        'base_address'  => env('SSO_BASE_ADDRESS'),
        'client_id'     => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
    ],
    'middlewares' => [
        'auth' => env('SSO_AUTH_GUARD', 'auth:sanctum'),
        'web'  => env('SSO_WEB_MIDDLEWARE', true),
        'sela' => env('SSO_SELA_MIDDLEWARE', true)
    ],
    'routes'      => [
        'login_get'      => env('SSO_LOGIN_ROUTE', 'sso-login'),
        'login_redirect' => env('SSO_GET_LOGIN_URL_ROUTE', 'sso-get-login'),
        'login_verify'   => env('SSO_VERIFY_ROUTE', 'sso-verify-login'),
        'login_callback' => env('SSO_CALLBACK_ROUTE', 'sso-callback'),
        'logout'         => env('SSO_LOGOUT_ROUTE', 'sso-logout'),
    ]
];
