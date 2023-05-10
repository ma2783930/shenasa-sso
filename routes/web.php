<?php

use Illuminate\Support\Facades\Route;
use Shenasa\Facades\Sso;
use Shenasa\Http\Controllers\SsoAuthController;

if (Sso::getIsEnable()) {
    Route::prefix('sso')->middleware('web')->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', [SsoAuthController::class, 'login'])->name('sso-auth.login');
            Route::get('callback', [SsoAuthController::class, 'callback'])->name('sso-auth.callback');
        });
        Route::middleware(config('sso.auth_middleware'))->group(function () {
            Route::get('logout', [SsoAuthController::class, 'logout'])->name('sso-auth.logout');
        });
    });
}
