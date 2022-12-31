<?php

use Illuminate\Support\Facades\Route;
use Shenasa\Facades\Sso;
use Shenasa\Http\Controllers\SsoAuthController;

if (Sso::getIsEnable()) {
    Route::middleware('web')->group(function(){
        Route::middleware('guest')->group(function(){
            Route::get(config('sso.login_url'), [SsoAuthController::class, 'login'])->name('sso-auth.login');
            Route::get(config('sso.callback_url'), [SsoAuthController::class, 'callback'])->name('sso-auth.callback');
        });
        Route::middleware(config('sso.auth_middleware'))->group(function(){
            Route::get(config('sso.logout_url'), [SsoAuthController::class, 'logout'])->name('sso-auth.logout');
        });
    });
}
