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
use Platform\Media\Styles\Style;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Styles\Macros\ResizeMacro;

class ResizeMacroTest extends IlluminateTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		$this->app['cartalyst.filesystem']  = m::mock('Illuminate\Filesystem\Filesystem');
		$this->app['image']       = m::mock('Intervention\Image\ImageManager');
		$this->app['path.public'] = '/';

		$this->macro = new ResizeMacro($this->app);

		$style = new Style('foo');
		$style->width = 200;
		$style->height = 200;

		$this->macro->setStyle($style);
	}

	/** @test */
	public function it_can_set_and_retrieve_styles()
	{
		$style = new Style('foo');

		$this->macro->setStyle($style);

		$this->assertSame($style, $this->macro->getStyle());
	}

	/** @test */
	public function it_can_run_macros_up()
	{
		$image    = m::mock('Intervention\Image\ImageManager');
		$media    = m::mock('Platform\Media\Models\Media');
		$file     = m::mock('Cartalyst\Filesystem\File');
		$uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

		$media->shouldReceive('getAttribute');
		$media->shouldReceive('setAttribute');

		$media->shouldReceive('save')
			->once();

		$file->shouldReceive('isImage')
			->once()
			->andReturn(true);

		$file->shouldReceive('getFilename')
			->once()
			->andReturn('foo');

		$file->shouldReceive('getExtension')
			->once()
			->andReturn('jpg');

		$file->shouldReceive('getContents')
			->once();

		$this->app['image']->shouldReceive('make')
			->once()
			->andReturn($image);

		$image->shouldReceive('resize')
			->with('', 200, m::any())
			->once()
			->andReturn($image);

		$image->shouldReceive('save')
			->with('/_foo-200-200.jpg')
			->once();

		$this->macro->up($media, $file, $uploaded);
	}

	/** @test */
	public function it_can_run_macros_down()
	{
		$media = m::mock('Platform\Media\Models\Media');
		$file  = m::mock('Cartalyst\Filesystem\File');

		$media->shouldReceive('getAttribute');

		$file->shouldReceive('getFilename')
			->once()
			->andReturn('foo');

		$file->shouldReceive('getExtension')
			->once()
			->andReturn('jpg');

		\Illuminate\Support\Facades\File::shouldReceive('delete')
			->with('/_foo-200-200.jpg')
			->once();

		$this->macro->down($media, $file);
	}

}
