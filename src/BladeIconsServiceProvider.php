<?php

namespace DigitLab\BladeIcons;

use Illuminate\Support\ServiceProvider;

class BladeIconsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();

        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('icon', function ($expression) {
            return "<?php echo app('icon.renderer')->render$expression; ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('icon.renderer', function ($app) {
            $paths = $app['config']['icon.paths'];
            $cache = $app['config']['icon.compiled'];

            return new IconRenderer($app['files'], $paths, $cache);
        });

        $this->app->alias('icon.renderer', IconRenderer::class);
    }

    protected function registerConfig()
    {
        $configPath = __DIR__.'/../config/icon.php';

        $this->publishes([$configPath => config_path('icon.php')]);

        $this->mergeConfigFrom($configPath, 'icon');
    }
}
