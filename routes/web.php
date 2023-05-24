<?php

use Illuminate\Support\Facades\Route;
use Sela\Middleware\SelaLogHandler;
use Shenasa\Facades\Sso;
use Shenasa\Http\Controllers\SsoAuthController;

$middlewares = [];

if (config('sso.middlewares.web')) $middlewares[] = 'web';
if (config('sso.middlewares.sela')) $middlewares[] = SelaLogHandler::class;

if (Sso::getIsEnable()) {
    Route::name('sso-auth.')
         ->controller(SsoAuthController::class)
         ->middleware($middlewares)
         ->group(function () {
             Route::middleware('guest')->group(function () {
                 Route::get(config('sso.routes.login_get'), 'getLogin')->name('get-login-url');
                 Route::post(config('sso.routes.login_verify'), 'verifyLogin')->name('verify-login');
                 Route::get(config('sso.routes.login_redirect'), 'loginRedirect')->name('login');
                 Route::get(config('sso.routes.login_callback'), 'callback')->name('callback');
             });

             Route::middleware(config('sso.middlewares.auth'))->group(function () {
                 Route::get(config('sso.routes.logout'), 'logout')->name('logout');
             });
         });
}
