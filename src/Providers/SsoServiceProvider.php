<?php

namespace Shenasa\Providers;

use Illuminate\Support\ServiceProvider;
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

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'sso');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        $loadMigrations = config('sso.load_migrations', false);
        if ($loadMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
