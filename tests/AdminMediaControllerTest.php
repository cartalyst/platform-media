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
 * @version    5.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Media\Controllers\Admin\MediaController;

class AdminMediaControllerTest extends IlluminateTestCase
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

        // Media Repository
        $this->media       = m::mock('Platform\Media\Repositories\MediaRepositoryInterface');
        $this->roles       = m::mock('Platform\Roles\Repositories\RoleRepositoryInterface');
        $this->tags        = m::mock('Platform\Tags\Repositories\TagsRepositoryInterface');
        $this->namespaces  = m::mock('Platform\Attributes\Repositories\ManagerRepositoryInterface');

        // Media Controller
        $this->controller = new MediaController($this->media, $this->roles, $this->tags, $this->namespaces);
    }

    /** @test */
    public function index_route()
    {
        $this->media->shouldReceive('getAllowedMimes')
            ->once()
            ->andReturn([]);

        $this->media->shouldReceive('getAllTags')
            ->once();

        $this->roles->shouldReceive('findAll')
            ->once();

        $this->app['view']->shouldReceive('make')
            ->once();

        $this->controller->index();
    }

    /** @test */
    public function edit_route()
    {
        $model = m::mock('Platform\Media\Models\Media');

        $this->media->shouldReceive('getAllTags')
            ->once();

        $this->roles->shouldReceive('findAll')
            ->once();

        $this->namespaces->shouldReceive('getNamespaces')
            ->once();

        $this->media->shouldReceive('find')
            ->once()
            ->andReturn($model);

        $this->app['view']->shouldReceive('make')
            ->once();

        $this->controller->edit(1);
    }

    /** @test */
    public function edit_non_existing()
    {
        $this->app['alerts']->shouldReceive('error')
            ->once();

        $this->app['translator']->shouldReceive('trans')
            ->once();

        $this->app['redirect']->shouldReceive('route')
            ->once()
            ->andReturn($response = m::mock('Illuminate\Response\Response'));

        $this->media->shouldReceive('find');

        $this->controller->edit(1);
    }

    /** @test */
    public function datagrid()
    {
        $this->app['datagrid']->shouldReceive('make')
            ->once();

        $this->media->shouldReceive('grid')
            ->once()
            ->andReturn([]);

        $this->controller->grid();
    }

    /** @test */
    public function upload_route()
    {
        $this->app['request']->shouldReceive('file')
            ->once()
            ->andReturn($file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile'));

        $this->app['request']->shouldReceive('input')
            ->once()
            ->andReturn([]);

        $this->media->shouldReceive('validForUpload')
            ->once()
            ->andReturn(true);

        $this->media->shouldReceive('upload')
            ->once()
            ->andReturn($media = m::mock('Platform\Media\Models\Media'));

        $this->app['response']->shouldReceive('make')
            ->with($media, 200, [])
            ->once();

        $this->controller->upload();
    }

    /** @test */
    public function upload_invalid_route()
    {
        $this->app['request']->shouldReceive('file')
            ->once()
            ->andReturn($file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile'));

        $this->media->shouldReceive('validForUpload')
            ->once()
            ->andReturn(false);

        $this->media->shouldReceive('getError')
            ->once();

        $this->app['response']->shouldReceive('make')
            ->with(null, 400, [])
            ->once();

        $this->controller->upload();
    }

    /** @test */
    public function update_route()
    {
        $this->app['alerts']->shouldReceive('success');
        $this->app['translator']->shouldReceive('trans');

        $this->app['request']->shouldReceive('except')
            ->with('file')
            ->once()
            ->andReturn(['name' => 'foo']);

        $this->app['request']->shouldReceive('ajax')
            ->once()
            ->andReturn(true);

        $this->app['request']->shouldReceive('file')
            ->once()
            ->andReturn($file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile'));

        $this->media->shouldReceive('validForUpdate')
            ->once()
            ->andReturn(true);

        $this->media->shouldReceive('update')
            ->once()
            ->andReturn($media = m::mock('Platform\Media\Models\Media'));

        $this->app['response']->shouldReceive('make')
            ->with(null, 200, [])
            ->once();

        $this->controller->update(1);
    }

    /** @test */
    public function update_invalid_route()
    {
        $this->app['alerts']->shouldReceive('error');

        $this->app['translator']->shouldReceive('trans');

        $this->app['redirect']->shouldReceive('back')
            ->once()
            ->andReturn($this->app['redirect']);

        $this->app['request']->shouldReceive('except')
            ->with('file')
            ->once()
            ->andReturn(['name' => 'foo']);

        $this->media->shouldReceive('validForUpdate')
            ->once()
            ->andReturn(false);

        $this->app['request']->shouldReceive('ajax')
            ->once()
            ->andReturn(false);

        $this->media->shouldReceive('getError')
            ->once();

        $this->controller->update(1);
    }

    /** @test */
    public function delete_route()
    {
        $this->app['alerts']->shouldReceive('success');
        $this->app['translator']->shouldReceive('trans');

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $this->media->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->controller->delete(1);
    }

    /** @test */
    public function delete_not_existing_route()
    {
        $this->app['alerts']->shouldReceive('error');
        $this->app['translator']->shouldReceive('trans');

        $this->app['redirect']->shouldReceive('route')
            ->once();

        $this->media->shouldReceive('delete')
            ->once();

        $this->controller->delete(1);
    }

    /** @test */
    public function execute_action()
    {
        $this->app['request']->shouldReceive('input')
            ->with('action')
            ->once()
            ->andReturn('delete');

        $this->app['request']->shouldReceive('input')
            ->with('rows', [])
            ->once()
            ->andReturn([1]);

        $this->media->shouldReceive('delete')
            ->with(1)
            ->once();

        $this->app['response']->shouldReceive('make')
            ->with('Success', 200, [])
            ->once();

        $this->controller->executeAction();
    }

    /** @test */
    public function execute_non_existing_action()
    {
        $this->app['request']->shouldReceive('input')
            ->with('action')
            ->once()
            ->andReturn('foobar');

        $this->app['response']->shouldReceive('make')
            ->with('Failed', 500, [])
            ->once();

        $this->controller->executeAction();
    }
}
