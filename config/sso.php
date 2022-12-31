<?php

return [
    'enable'          => env('SSO_ENABLE', true),
    'base_address'    => env('SSO_BASE_ADDRESS'),
    'client_id'       => env('SSO_CLIENT_ID'),
    'client_secret'   => env('SSO_CLIENT_SECRET'),
    'login_url'       => env('SSO_LOGIN_URL', 'sso-login'),
    'logout_url'      => env('SSO_LOGOUT_URL', 'sso-logout'),
    'callback_url'    => env('SSO_CALLBACK_URL', 'sso-callback'),
    'auth_middleware' => env('SSO_AUTH_GUARD', 'auth:sanctum')
];
