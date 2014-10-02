<?php namespace Platform\Media\Providers;
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Platform\Media\Repositories\IlluminateMediaRepository;

class MediaServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->package('platform/media', 'platform/media'. __DIR__.'/../..');

		require __DIR__.'/../functions.php';

		$this->registerBladeMediaWidget();

		// Register the event handler
		$this->app['events']->subscribe('Platform\Media\Handlers\MediaEventHandler');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerMediaRepository();

		$this->registerFilesystemPackage();

		$this->registerInterventionPackage();
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
	 * Register the media repository.
	 *
	 * @return void
	 */
	protected function registerMediaRepository()
	{
		$mediaRepository = 'Platform\Media\Repositories\MediaRepositoryInterface';

		if ( ! $this->app->bound($mediaRepository))
		{
			$this->app->bind($mediaRepository, function($app)
			{
				$model = get_class($app['Platform\Media\Models\Media']);

				return (new IlluminateMediaRepository($model))
					->setDispatcher($app['events']);
			});
		}
	}

	/**
	 * Register the Blade @media extension.
	 *
	 * @return void
	 */
	protected function registerBladeMediaWidget()
	{
		// Register @media blade extension
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();
		$blade->extend(function($value) use ($blade)
		{
			$matcher = '/(\s*)@media(\(.*?\)\s*)/';

			return preg_replace($matcher, '<?php echo Widget::make("platform/media::media.show", array$2); ?>', $value);
		});
	}

}
