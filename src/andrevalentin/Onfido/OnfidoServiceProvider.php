<?php

namespace andrevalentin\Onfido;

use Illuminate\Support\ServiceProvider;
use Onfido\Api\DefaultApi;
use Onfido\Configuration;

class OnfidoServiceProvider extends ServiceProvider
{
    /**
     * Indicates if the laoding of the provider is defered.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $config = new Configuration();
        $config->setApiKey('Authorization', 'token=' . config('onfido.api_key'));
        $config->setApiKeyPrefix('Authorization', 'Token');

        $this->app->singleton('onfido', function ($app) use ($config) {
            return new DefaultApi(null, $config);
        });

        $this->app->alias('onfido', DefaultApi::class);
    }

    /**
     * Get the service provided by the provider.
     *
     * @return array|string
     */
    public function provides()
    {
        return ['onfido', DefaultApi::class];
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $source = dirname(__DIR__, 2) . '/config/onfido.php';

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                $source => config_path('onfido.php'),
            ], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('onfido');
        }

        $this->mergeConfigFrom($source, 'onfido');
    }
}