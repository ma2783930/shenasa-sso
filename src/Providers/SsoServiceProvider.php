<?php

namespace Shenasa\Providers;

use Illuminate\Support\ServiceProvider;
use Shenasa\Actions\SsoActiveUserProviderAction;
use Shenasa\Actions\SsoCallbackFailureAction;
use Shenasa\Actions\SsoLoginAction;
use Shenasa\Actions\SsoLogoutAction;
use Shenasa\Actions\SsoUserFinderAction;
use Shenasa\Contracts\SsoActiveUserProviderContract;
use Shenasa\Contracts\SsoCallbackFailureHandlerContract;
use Shenasa\Contracts\SsoLoginActionContract;
use Shenasa\Contracts\SsoLogoutActionContract;
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
        $this->app->bind('sso', function () {
            return new SsoHelper;
        });

        $this->app->singleton(SsoLoginActionContract::class, SsoLoginAction::class);
        $this->app->singleton(SsoLogoutActionContract::class, SsoLogoutAction::class);
        $this->app->singleton(SsoUserFinderContract::class, SsoUserFinderAction::class);
        $this->app->singleton(SsoCallbackFailureHandlerContract::class, SsoCallbackFailureAction::class);
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
//            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../../config/sso.php'      => config_path('sso.php')
        ], 'shenasa-sso');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'sso');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
    }
}
