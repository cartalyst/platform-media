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
 * @version    2.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Handlers\EventHandler;

class MediaEventHandlerTest extends IlluminateTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$this->app['platform.media.manager'] = m::mock('Platform\Media\Styles\Manager');

		$this->handler = new EventHandler($this->app);
	}

	/** @test */
	public function test_subscribe()
	{
		$dispatcher = m::mock('Illuminate\Events\Dispatcher');

		$dispatcher->shouldReceive('listen')->once()->with('platform.media.uploaded', get_class($this->handler).'@uploaded');

		$dispatcher->shouldReceive('listen')->once()->with('platform.media.updating', get_class($this->handler).'@updating');
		$dispatcher->shouldReceive('listen')->once()->with('platform.media.updated', get_class($this->handler).'@updated');

		$dispatcher->shouldReceive('listen')->once()->with('platform.media.deleting', get_class($this->handler).'@deleting');
		$dispatcher->shouldReceive('listen')->once()->with('platform.media.deleted', get_class($this->handler).'@deleted');

		$this->handler->subscribe($dispatcher);
	}

	/** @test */
	public function test_on_uploaded()
	{
		$media        = m::mock('Platform\Media\Models\Media');
		$file         = m::mock('Cartalyst\Filesystem\File');
		$uploadedFile = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

		$this->app['files']->shouldReceive('delete')
			->with('foo')
			->once();

		$this->app['platform.media.manager']->shouldReceive('handleUp')
			->once();

		$media->shouldReceive('getAttribute')
			->once()
			->with('thumbnail')
			->andReturn('foo');

		$this->shouldFlushCache($media);

		$this->handler->uploaded($media, $file, $uploadedFile);
	}

	/** @test */
	public function test_on_updated()
	{
		$media = m::mock('Platform\Media\Models\Media');

		$this->shouldFlushCache($media);

		$this->handler->updated($media);
	}

	/** @test */
	public function test_on_deleting()
	{
		$media = m::mock('Platform\Media\Models\Media');
		$file  = m::mock('Cartalyst\Filesystem\File');

		$this->app['platform.media.manager']->shouldReceive('handleDown')
			->once();

		$this->handler->deleting($media, $file);
	}

	/** @test */
	public function test_on_deleted()
	{
		$media = m::mock('Platform\Media\Models\Media');

		$this->shouldFlushCache($media);

		$this->handler->deleted($media);
	}

	/**
	 * Sets expected method calls for flushing cache.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return void
	 */
	protected function shouldFlushCache($media)
	{
		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.media.1');

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.media.path.foo');

		$media->shouldReceive('getAttribute')
			->once()
			->with('id')
			->andReturn(1);

		$media->shouldReceive('getAttribute')
			->once()
			->with('path')
			->andReturn('foo');
	}

}
