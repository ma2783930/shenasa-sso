<?php

return [
    'enable'            => env('SSO_ENABLE', true),
    'base_address'      => env('SSO_BASE_ADDRESS'),
    'client_id'         => env('SSO_CLIENT_ID'),
    'client_secret'     => env('SSO_CLIENT_SECRET'),
    'auth_middleware'   => env('SSO_AUTH_GUARD', 'auth:sanctum'),
    'login_route'       => env('SSO_LOGIN_ROUTE', 'sso-login'),
    'get_state_route'   => env('SSO_STATE_ROUTE', 'sso-state'),
    'async_login_route' => env('ASYNC_LOGIN_ROUTE', 'sso-login'),
    'logout_route'      => env('LOGOUT_ROUTE', 'sso-logout'),
    'callback_route'    => env('CALLBACK_ROUTE', 'sso-callback'),
    'web_middleware'    => env('WEB_MIDDLEWARE', true)
];
