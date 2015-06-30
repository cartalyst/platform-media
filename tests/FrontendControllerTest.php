<?php namespace Platform\Media\Tests;
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
 * @version    1.0.7
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Controllers\Frontend\MediaController;

class FrontendMediaControllerTest extends IlluminateTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		// Admin Controller expectations
		$this->app['sentinel']->shouldReceive('getUser');
		$this->app['view']->shouldReceive('share');
		$this->app['filesystem'] = m::mock('Cartalyst\Filesystem\FilesystemManager');

		// Media Repository
		$this->media = m::mock('Platform\Media\Repositories\MediaRepositoryInterface');

		// Media Controller
		$this->controller = new MediaController($this->media);
	}

	/** @test */
	public function it_can_view_media()
	{
		$media = m::mock('Platform\Media\Models\Media');

		$this->media->shouldReceive('findByPath')
			->with('foo')
			->once()
			->andReturn($media);

		$media->shouldReceive('getAttribute')
			->with('private')
			->once()
			->andReturn(false);

		$media->shouldReceive('getAttribute')
			->with('path')
			->once()
			->andReturn('foo');

		$media->shouldReceive('getAttribute')
			->with('mime')
			->once();

		$this->app['filesystem']->shouldReceive('read')
			->once();

		$this->controller->view('foo');
	}

	/** @test */
	public function it_can_download_media()
	{
		$media = m::mock('Platform\Media\Models\Media');

		$this->media->shouldReceive('findByPath')
			->with('foo')
			->once()
			->andReturn($media);

		$media->shouldReceive('getAttribute')
			->with('private')
			->once()
			->andReturn(false);

		$media->shouldReceive('getAttribute')
			->with('path')
			->once()
			->andReturn('foo');

		$media->shouldReceive('getAttribute')
			->with('mime')
			->once();

		$media->shouldReceive('getAttribute')
			->with('name')
			->once();

		$this->controller->download('foo');
	}

}
