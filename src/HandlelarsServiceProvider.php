<?php namespace Handlelars;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewFinderInterface;

class HandlelarsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        die('yata');
        $this->setupConfig();

        $this->registerMustacheEngine();

        $this->registerMustacheViewExtension();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['handlelars', 'mustache.engine'];
    }

    private function setupConfig()
    {
        $config = __DIR__ . '/config/config.php';
        $this->mergeConfigFrom($config, 'handlelars');
    }

    private function registerMustacheEngine()
    {
        $this->app->bind('mustache.engine', function() {
            return $this->app->make('Handlelars\MustacheEngine');
        });
    }

    private function registerMustacheViewExtension()
    {
        $this->app['view']->addExtension(
            'mustache',
            'mustache',
            function () {
                return $this->app['mustache.engine'];
            }
        );
    }
}
