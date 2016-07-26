<?php

/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    4.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Providers;

use Platform\Media\Commands;
use Cartalyst\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Register the tags namespace
        $this->app['platform.tags.manager']->registerNamespace(
            $this->app['Platform\Media\Models\Media']
        );

        // Register the event handler
        $this->app['events']->subscribe('platform.media.handler.event');

        // Register the Blade @media extension
        $this->registerBladeMediaWidget();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->prepareResources();

        // Register the Cartalyst Filesystem Service Provider and Facade alias
        $this->registerFilesystemPackage();

        // Register the Intervention Service Provider and Facade alias
        $this->registerInterventionPackage();

        // Register the commands
        $this->registerCommands();

        // Register the repository
        $this->bindIf('platform.media', 'Platform\Media\Repositories\MediaRepository');

        // Register the validator
        $this->bindIf('platform.media.validator', 'Platform\Media\Validator\MediaValidator');

        // Register the manager
        $this->bindIf('platform.media.manager', 'Platform\Media\Styles\Manager', true, false);

        // Register the event handler
        $this->bindIf('platform.media.handler.event', 'Platform\Media\Handlers\EventHandler');
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $config = realpath(__DIR__.'/../../config/config.php');

        $this->mergeConfigFrom($config, 'platform-media');

        $this->publishes([
            $config => config_path('platform-media.php'),
        ], 'config');
    }

    /**
     * Register the Cartalyst Interpret Service Provider and Facade alias.
     *
     * @return void
     */
    protected function registerFilesystemPackage()
    {
        $serviceProvider = 'Cartalyst\Filesystem\Laravel\FilesystemServiceProvider';

        if (! $this->app->getProvider($serviceProvider)) {
            // Register the Filesystem Service provider and class alias
            $this->app->register($serviceProvider);

            AliasLoader::getInstance()->alias('Filesystem', 'Cartalyst\Filesystem\Laravel\Facades\Filesystem');
        }
    }

    /**
     * Register the Intervention Image Service Provider and Facade alias.
     *
     * @return void
     */
    protected function registerInterventionPackage()
    {
        $serviceProvider = 'Intervention\Image\ImageServiceProvider';

        if (! $this->app->getProvider($serviceProvider)) {
            // Register the Intervention Image service provider and class alias
            $this->app->register($serviceProvider);

            AliasLoader::getInstance()->alias('Image', 'Intervention\Image\Facades\Image');
        }
    }

    /**
     * Register the Blade @media extension.
     *
     * @return void
     */
    protected function registerBladeMediaWidget()
    {
        $compiler = $this->app['blade.compiler'];

        $compiler->directive('mediaPath', function ($value) {
            return "<?php echo Widget::make('platform/media::media.path', array$value); ?>";
        });

        $compiler->directive('mediaUpload', function ($value) {
            return "<?php echo Widget::make('platform/media::media.upload', array$value); ?>";
        });
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app['command.platform.media.images.clear'] = $this->app->share(function ($app) {
            return new Commands\ImagesClear($app);
        });

        $this->commands('command.platform.media.images.clear');

        $this->app['command.platform.media.images.generate'] = $this->app->share(function ($app) {
            return new Commands\ImagesGenerate($app);
        });

        $this->commands('command.platform.media.images.generate');
    }
}
