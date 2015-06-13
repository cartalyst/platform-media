<?php namespace Platform\Media\Providers;
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
 * @version    1.0.6
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class MediaServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		// Register the extension component namespaces
		$this->package('platform/media', 'platform/media'. __DIR__.'/../..');

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
	 * {@inheritDoc}
	 */
	public function register()
	{
		// Register the Cartalyst Filesystem Service Provider and Facade alias.
		$this->registerFilesystemPackage();

		// Register the Intervention Service Provider and Facade alias.
		$this->registerInterventionPackage();

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
	 * Register the Cartalyst Interpret Service Provider and Facade alias.
	 *
	 * @return void
	 */
	protected function registerFilesystemPackage()
	{
		$serviceProvider = 'Cartalyst\Filesystem\Laravel\FilesystemServiceProvider';

		if ( ! $this->app->getRegistered($serviceProvider))
		{
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

		if ( ! $this->app->getRegistered($serviceProvider))
		{
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

		$compiler->extend(function($value)
		{
			$matcher = '/(\s*)@media(\(.*?\)\s*)/';

			return preg_replace($matcher, '<?php echo Widget::make("platform/media::media.show", array$2); ?>', $value);
		});

		$compiler->extend(function($value)
		{
			$matcher = '/(\s*)@thumbnail(\(.*?\)\s*)/';

			return preg_replace($matcher, '<?php echo Widget::make("platform/media::media.thumbnail", array$2); ?>', $value);
		});
	}

}
