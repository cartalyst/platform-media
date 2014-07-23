<?php namespace Platform\Media;
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
 * @version    2.0.0
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
	public function register()
	{
		$app = $this->app;

		$this->registerMediaRepository($app);

		// Register our event handler
		$app['events']->subscribe(get_class(app('Platform\Media\Handlers\MediaEventHandler')));

		# need to check if the service provider is already registered
		# as we register it by default on Platform..
		//$this->registerFilesystemPackage($app);

		$this->registerInterventionPackage($app);
	}

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$app = $this->app;

		require __DIR__.'/functions.php';

		$this->registerBladeMediaWidget($app);
	}

	/**
	 * Register the Cartalyst Interpret Service Provider and Facade alias.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerFilesystemPackage($app)
	{
		// Register the Filesystem Service provider and class alias
		$app->register('Cartalyst\Filesystem\Laravel\FilesystemServiceProvider');

		AliasLoader::getInstance()->alias('Filesystem', 'Cartalyst\Filesystem\Laravel\Facades\Filesystem');
	}

	/**
	 * Register the Intervention Image Service Provider and Facade alias.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerInterventionPackage($app)
	{
		// Register the Intervention Image service provider and class alias
		$app->register('Intervention\Image\ImageServiceProvider');

		AliasLoader::getInstance()->alias('Image', 'Intervention\Image\Facades\Image');
	}

	/**
	 * Register the media repository.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerMediaRepository($app)
	{
		$mediaRepository = 'Platform\Media\Repositories\MediaRepositoryInterface';

		if ( ! $app->bound($mediaRepository))
		{
			$app->bind($mediaRepository, function($app)
			{
				$model = get_class($app['Platform\Media\Models\Media']);

				return new IlluminateMediaRepository($model);
			});
		}
	}

	/**
	 * Register the Blade @media extension.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerBladeMediaWidget($app)
	{
		// Register @media blade extension
		$blade = $app['view']->getEngineResolver()->resolve('blade')->getCompiler();
		$blade->extend(function($value) use ($blade)
		{
			$matcher = '/(\s*)@media(\(.*?\)\s*)/';

			return preg_replace($matcher, '<?php echo Widget::make("platform/media::media.show", array$2); ?>', $value);
		});
	}

}
