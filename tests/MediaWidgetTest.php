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
 * @version    3.1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Platform\Media\Widgets\Media;
use Cartalyst\Testing\IlluminateTestCase;

class MediaWidgetTest extends IlluminateTestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->media = m::mock('Platform\Media\Repositories\MediaRepositoryInterface');

        $this->widget = new Media($this->media);
    }

    /** @test */
    public function it_can_retrieve_the_thumbnail_url()
    {
        $media = m::mock('Platform\Media\Models\Media');

        $this->media->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($media);

        $media->shouldReceive('getAttribute')
            ->andReturn('foo');

        $this->app['url']->shouldReceive('to')
            ->with('foo', [], '')
            ->once();

        $this->widget->show(1, 'thumbnail');
    }

    /** @test */
    public function it_can_retrieve_the_download_url()
    {
        $media = m::mock('Platform\Media\Models\Media');

        $this->media->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($media);

        $media->shouldReceive('getAttribute')
            ->with('path')
            ->once()
            ->andReturn('foo');

        $this->app['url']->shouldReceive('route')
            ->with('media.download', 'foo', true, '')
            ->once();

        $this->widget->show(1, 'download');
    }

    /** @test */
    public function it_can_retrieve_the_media_url()
    {
        $media = m::mock('Platform\Media\Models\Media');

        $this->media->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($media);

        $media->shouldReceive('getAttribute')
            ->with('path')
            ->once()
            ->andReturn('foo');

        $this->app['url']->shouldReceive('route')
            ->with('media.view', 'foo', true, '')
            ->once();

        $this->widget->show(1);
    }
}
