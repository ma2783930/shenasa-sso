<?php

use Illuminate\Support\Facades\Route;
use Shenasa\Facades\Sso;
use Shenasa\Http\Controllers\SsoAuthController;

if (Sso::getIsEnable()) {
    Route::name('sso-auth.')
         ->controller(SsoAuthController::class)
         ->middleware(config('sso.web_middleware') ? 'web' : [])
         ->group(function () {
             Route::middleware('guest')->group(function () {
                 Route::get(config('sso.get_state_route'), 'state')->name('state');
                 Route::post(config('sso.async_login_route'), 'asyncLogin')->name('async-login');
                 Route::get(config('sso.login_route'), 'login')->name('login');
                 Route::get(config('sso.callback_route'), 'callback')->name('callback');
             });

             Route::middleware(config('sso.auth_middleware'))->group(function () {
                 Route::get(config('sso.logout_route'), 'logout')->name('logout');
             });
         });
}
