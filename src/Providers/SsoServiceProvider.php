<?php

namespace Shenasa\Providers;

use Illuminate\Support\ServiceProvider;
use Shenasa\Actions\SsoActiveUserProviderAction;
use Shenasa\Actions\SsoAsyncLogin;
use Shenasa\Actions\SsoCallbackFailureAction;
use Shenasa\Actions\SsoLogin;
use Shenasa\Actions\SsoLogout;
use Shenasa\Actions\SsoUserFinderAction;
use Shenasa\Contracts\SsoActiveUserProviderContract;
use Shenasa\Contracts\SsoAsyncLoginContract;
use Shenasa\Contracts\SsoCallbackFailureContract;
use Shenasa\Contracts\SsoLoginContract;
use Shenasa\Contracts\SsoLogoutContract;
use Shenasa\Contracts\SsoUserFinderContract;
use Shenasa\SsoHelper;

class SsoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('sso', fn() => new SsoHelper);

        $this->app->singleton(SsoLoginContract::class, SsoLogin::class);
        $this->app->singleton(SsoAsyncLoginContract::class, SsoAsyncLogin::class);
        $this->app->singleton(SsoLogoutContract::class, SsoLogout::class);
        $this->app->singleton(SsoUserFinderContract::class, SsoUserFinderAction::class);
        $this->app->singleton(SsoCallbackFailureContract::class, SsoCallbackFailureAction::class);
        $this->app->singleton(SsoActiveUserProviderContract::class, SsoActiveUserProviderAction::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sso.php', 'sso'
        );

        $this->publishes([
            __DIR__ . '/../../config/sso.php' => config_path('sso.php')
        ], 'shenasa-sso');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'sso');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
