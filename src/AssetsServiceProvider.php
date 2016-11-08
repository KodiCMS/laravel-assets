<?php

namespace KodiCMS\Assets;

use Illuminate\Support\ServiceProvider;
use KodiCMS\Assets\Console\Commands\PackagesListCommand;

class AssetsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('assets.packages', function ($app) {
            return new PackageManager();
        });

        $this->app->singleton('assets', function ($app) {
            return new Assets($app['assets.packages']);
        });

        $this->app->singleton('assets.meta', function ($app) {
            return new Meta($app['assets']);
        });

        $aliases = [
            'assets.meta'     => \KodiCMS\Assets\Contracts\MetaInterface::class,
            'assets'          => \KodiCMS\Assets\Contracts\AssetsInterface::class,
            'assets.packages' => \KodiCMS\Assets\Contracts\PackageManagerInterface::class,
        ];

        foreach ($aliases as $key => $alias) {
            $this->app->alias($key, $alias);
        }

        $this->commands(PackagesListCommand::class);
    }

    /**
     * Get a list of files that should be compiled for the package.
     *
     * @return array
     */
    public static function compiles()
    {
        return [
            base_path('vendor\kodicms\laravel-assets\src\Contracts\MetaInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Contracts\AssetsInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Contracts\PackageManagerInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Contracts\AssetElementInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Contracts\PackageInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Contracts\SocialMediaTagsInterface.php'),
            base_path('vendor\kodicms\laravel-assets\src\Traits\Groups.php'),
            base_path('vendor\kodicms\laravel-assets\src\Traits\Vars.php'),
            base_path('vendor\kodicms\laravel-assets\src\Traits\Packages.php'),
            base_path('vendor\kodicms\laravel-assets\src\Traits\Styles.php'),
            base_path('vendor\kodicms\laravel-assets\src\Traits\Scripts.php'),
            base_path('vendor\kodicms\laravel-assets\src\AssetElement.php'),
            base_path('vendor\kodicms\laravel-assets\src\Css.php'),
            base_path('vendor\kodicms\laravel-assets\src\Javascript.php'),
            base_path('vendor\kodicms\laravel-assets\src\Html.php'),
            base_path('vendor\kodicms\laravel-assets\src\Meta.php'),
            base_path('vendor\kodicms\laravel-assets\src\Package.php'),
            base_path('vendor\kodicms\laravel-assets\src\PackageManager.php'),
            base_path('vendor\kodicms\laravel-assets\src\Assets.php'),
            base_path('vendor\kodicms\laravel-assets\src\Facades\Meta.php'),
        ];
    }
}
