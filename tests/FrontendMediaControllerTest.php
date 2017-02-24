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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Controllers\Frontend\MediaController;

class FrontendMediaControllerTest extends IlluminateTestCase
{
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
        $this->app['cartalyst.filesystem'] = m::mock('Cartalyst\Filesystem\FilesystemManager');

        // Media Repository
        $this->media = m::mock('Platform\Media\Repositories\MediaRepositoryInterface');

        // Media Controller
        $this->controller = new MediaController($this->media);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
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

        $this->app['cartalyst.filesystem']->shouldReceive('read')
            ->once();

        $this->app['Illuminate\Contracts\Routing\ResponseFactory']->shouldReceive('make')
            ->with(null, 200)
            ->once()
            ->andReturn($response = m::mock('Symfony\Component\HttpFoundation\Response'));

        $response->shouldReceive('header')
            ->with('Content-Type', null)
            ->once();

        $response->shouldReceive('header')
            ->with('Content-Length', null)
            ->once();

        $response->shouldReceive('header')
            ->with('Cache-Control', m::any())
            ->once();

        $response->shouldReceive('header')
            ->with('ETag', m::any())
            ->once();

        $this->app['request']->shouldReceive('server')
            ->with('HTTP_IF_NONE_MATCH')
            ->once()
            ->andReturn(false);

        $this->controller->view('foo');
    }

    /**
     * @test
     * @runInSeparateProcess
     */
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

        $this->app['cartalyst.filesystem']->shouldReceive('read')
            ->once();

        $this->app['Illuminate\Contracts\Routing\ResponseFactory']->shouldReceive('make')
            ->with(null, 200)
            ->once()
            ->andReturn($response = m::mock('Symfony\Component\HttpFoundation\Response'));

        $response->shouldReceive('header')
            ->with('Connection', 'close')
            ->once();

        $response->shouldReceive('header')
            ->with('Content-Type', null)
            ->once();

        $response->shouldReceive('header')
            ->with('Content-Length', null)
            ->once();

        $response->shouldReceive('header')
            ->with('Content-Disposition', 'attachment; filename=""')
            ->once();

        $this->controller->download('foo');
    }
}
