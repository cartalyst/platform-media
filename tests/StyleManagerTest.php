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
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Platform\Media\Styles\Style;
use Platform\Media\Styles\Manager;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Styles\Macros\AbstractMacro;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StyleManagerTest extends IlluminateTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$config = [
			'styles' => function($manager) {},
			'macros' => function($manager) {},
		];

		$this->app['config'] = m::mock('Illuminate\Config\Repository');
		$this->app['config']->shouldReceive('get')
			->with('platform-media')
			->once()
			->andReturn($config);

		$this->manager = new Manager($this->app);
	}

	/** @test */
	public function it_can_set_and_retrieve_styles()
	{
		$styles = [
			'foo' => function() {},
			'bar' => function() {},
		];

		foreach ($styles as $key => $style)
		{
			$this->manager->setStyle($key, $style);
		}

		$this->assertSame($styles['foo'], array_get($this->manager->getStyles(), 'foo'));
		$this->assertSame($styles['bar'], array_get($this->manager->getStyles(), 'bar'));
	}

	/** @test */
	public function it_can_set_and_retrieve_macros()
	{
		$macros = [
			'resize' => 'ResizeMacro',
			'foo'    => 'FooMacro',
		];

		foreach ($macros as $key => $macro)
		{
			$this->manager->setMacro($key, $macro);
		}

		$this->assertSame($macros['resize'], array_get($this->manager->getMacros(), 'resize'));
		$this->assertSame($macros['foo'], array_get($this->manager->getMacros(), 'foo'));
	}

	/** @test */
	public function it_can_handle_up_and_down()
	{
		$style = function(Style $style)
		{
			$style->macros = ['resize'];
		};

		$media    = m::mock('Platform\Media\Models\Media');
		$file     = m::mock('Cartalyst\Filesystem\File');
		$uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

		$media->shouldReceive('up')
			->once();

		$this->manager->setMacro('resize', 'Platform\Media\Tests\FooMacro');
		$this->manager->setStyle('foo', $style);

		$uploaded->shouldReceive('getMimeType')
			->once();

		$this->manager->handleUp($media, $file, $uploaded);

		$file->shouldReceive('getMimetype')
			->once();

		$media->shouldReceive('down')
			->once();

		$this->manager->handleDown($media, $file);
	}

}

class FooMacro extends AbstractMacro {

	public function up(Media $media, File $file, UploadedFile $uploadedFile)
	{
		$media->up();
	}

	public function down(Media $media, File $file)
	{
		$media->down();
	}

}
