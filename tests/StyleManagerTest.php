<?php

/*
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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Platform\Media\Styles\Manager;
use Cartalyst\Testing\IlluminateTestCase;

class StyleManagerTest extends IlluminateTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(1);

        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['cartalyst.filesystem'] = m::mock('Cartalyst\Filesystem\Filesystem');
        $this->app['platform.media']       = m::mock('Platform\Media\Repositories\MediaRepositoryInterface');

        $this->manager = new Manager($this->app);
    }

    /** @test */
    public function it_can_set_and_retrieve_presets()
    {
        $presets = [
            'foo' => [],
            'bar' => [],
        ];

        $this->app['platform.media']->shouldReceive('createModel')
            ->twice()
            ->andReturn($media = m::mock('Platform\Media\Models\Media'))
        ;

        $media->shouldReceive('setPresets')
            ->twice()
        ;

        foreach ($presets as $key => $preset) {
            $this->manager->setPreset($key, $preset);
        }

        $this->assertInstanceOf('Platform\Media\Styles\Preset', $this->manager->getPreset('foo'));
        $this->assertInstanceOf('Platform\Media\Styles\Preset', $this->manager->getPreset('bar'));

        $this->assertSame('foo', $this->manager->getPreset('foo')->name);
        $this->assertSame('bar', $this->manager->getPreset('bar')->name);
    }

    /** @test */
    public function it_can_set_and_retrieve_macros()
    {
        $macros = [
            'resize' => 'ResizeMacro',
            'foo'    => 'FooMacro',
        ];

        foreach ($macros as $key => $macro) {
            $this->manager->setMacro($key, $macro);
        }

        $this->assertSame($macros['resize'], Arr::get($this->manager->getMacros(), 'resize'));
        $this->assertSame($macros['foo'], Arr::get($this->manager->getMacros(), 'foo'));
    }

    /** @test */
    public function it_can_handle_up_and_down()
    {
        $filePath = '2016/07/foo.jpg';
        $media    = m::mock('Platform\Media\Models\Media');
        $file     = m::mock('Cartalyst\Filesystem\File');
        $uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $this->app['platform.media']->shouldReceive('createModel')
            ->once()
            ->andReturn($media)
        ;

        $media->shouldReceive('setPresets')
            ->once()
        ;

        $media->shouldReceive('getAttribute')
            ->with('path')
            ->twice()
            ->andReturn($filePath)
        ;

        $this->app['cartalyst.filesystem']->shouldReceive('get')
            ->with($filePath)
            ->twice()
            ->andReturn($file)
        ;

        $preset = m::mock('Platform\Media\Styles\Preset');

        $preset->shouldReceive('setMedia')
            ->with($media)
            ->twice()
        ;

        $preset->shouldReceive('setFile')
            ->with($file)
            ->twice()
        ;

        $preset->shouldReceive('isValid')
            ->twice()
            ->andReturn(true)
        ;

        $preset->shouldReceive('applyMacros')
            ->with('up')
            ->once()
        ;

        $preset->shouldReceive('applyMacros')
            ->with('down')
            ->once()
        ;

        $this->manager->setPreset($preset);

        $this->manager->handleUp($media);

        $this->manager->handleDown($media);
    }
}
