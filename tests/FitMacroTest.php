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
 * @version    6.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Platform\Media\Macros\Fit;
use Platform\Media\Styles\Preset;
use Cartalyst\Testing\IlluminateTestCase;

class FitMacroTest extends IlluminateTestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['image']                 = m::mock('Intervention\Image\ImageManager');
        $this->app['path.public']           = '';

        $this->macro = new Fit($this->app);

        $this->preset = new Preset('foo', ['width' => 200, 'height' => 200, 'macros' => ['fit']]);

        $this->macro->setPreset($this->preset);
    }

    /** @test */
    public function it_can_run_macros_up()
    {
        $image    = m::mock('Intervention\Image\ImageManager');
        $media    = m::mock('Platform\Media\Models\Media');
        $file     = m::mock('Cartalyst\Filesystem\File');

        $file->shouldReceive('isImage')
            ->once()
            ->andReturn(true);

        $this->app['files']->shouldReceive('exists')
            ->with('/cache/media/foo')
            ->once()
            ->andReturn(false);

        $this->app['files']->shouldReceive('makeDirectory')
            ->with('/cache/media/foo')
            ->once();

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

        $image->shouldReceive('fit')
            ->with(200, 200, m::any())
            ->once()
            ->andReturn($image);

        $image->shouldReceive('save')
            ->with('/cache/media/foo/foo.jpg')
            ->once();

        $this->macro->up($media, $file);
    }

    /** @test */
    public function it_can_run_macros_down()
    {
        $media = m::mock('Platform\Media\Models\Media');
        $file  = m::mock('Cartalyst\Filesystem\File');

        $file->shouldReceive('isImage')
            ->once()
            ->andReturn(true);

        $this->app['files']->shouldReceive('exists')
            ->with('/cache/media/foo')
            ->once()
            ->andReturn(true);

        $file->shouldReceive('getFilename')
            ->once()
            ->andReturn('foo');

        $file->shouldReceive('getExtension')
            ->once()
            ->andReturn('jpg');

        $this->app['files']->shouldReceive('delete')
            ->with('/cache/media/foo/foo.jpg')
            ->once();

        $this->macro->down($media, $file);
    }
}
