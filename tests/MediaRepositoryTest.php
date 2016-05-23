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
 * @version    3.2.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;

class MediaRepositoryTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Additional Bindings
        $this->app['cartalyst.filesystem']                  = m::mock('Cartalyst\Filesystem\Filesystem');
        $this->app['platform.content']            = m::mock('Platform\Content\Repositories\ContentRepositoryInterface');
        $this->app['platform.media.handler.data'] = m::mock('Platform\Media\Handlers\DataHandlerInterface');
        $this->app['platform.media.manager']      = m::mock('Platform\Media\Repositories\ManagerRepository');
        $this->app['platform.media.validator']    = m::mock('Cartalyst\Support\Validator');
        $this->app['platform.menus']              = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
        $this->app['platform.menus.manager']      = m::mock('Platform\Menus\Repositories\ManagerRepositoryInterface');
        $this->app['platform.permissions']        = m::mock('Platform\Permissions\Repositories\PermissionsRepositoryInterface');
        $this->app['platform.roles']              = m::mock('Platform\Roles\Repositories\RoleRepositoryInterface');
        $this->app['platform.tags']               = m::mock('Platform\Tags\Repositories\TagsRepositoryInterface');
        $this->app['themes']                      = m::mock('Cartalyst\Themes\ThemeBag');

        $this->app['platform.menus.manager']->shouldIgnoreMissing();

        // Repository
        $this->repository = m::mock('Platform\Media\Repositories\MediaRepository[createModel]', [$this->app]);
    }

    /** @test */
    public function it_can_generate_the_grid()
    {
        $model = $this->shouldReceiveCreateModel();

        $this->repository->grid();
    }

    /** @test */
    public function it_can_find_records_by_id()
    {
        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.1', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($model);

        $model->shouldReceive('find')
            ->once();

        $this->repository->find(1);
    }

    /** @test */
    public function it_can_find_records_by_path()
    {
        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.path.foo', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($model);

        $model->shouldReceive('wherePath')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('first')
            ->once();

        $this->repository->findByPath('foo');
    }

    /** @test */
    public function it_can_retrieve_all_tags()
    {
        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('allTags')
            ->once()
            ->andReturn($collection = m::mock('Illuminate\Support\Collection'));

        $collection->shouldReceive('lists')
            ->with('name')
            ->once();

        $this->repository->getAllTags();
    }

    /** @test */
    public function it_can_validate_for_upload()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $this->app['cartalyst.filesystem']->shouldIgnoreMissing();

        $this->assertTrue($this->repository->validForUpload($file));
    }

    /** @test */
    public function it_sets_an_error_on_invalid_files_exceptions()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $error = 'Invalid file.';

        $this->app['translator']->shouldReceive('trans')
            ->with('platform/media::message.invalid_file', [], 'messages', '')
            ->once()
            ->andReturn($error);

        $this->app['cartalyst.filesystem']->shouldReceive('validateFile')
            ->once()
            ->andThrow(new \Cartalyst\Filesystem\Exceptions\InvalidFileException);

        $this->repository->validForUpload($file);

        $this->assertEquals($error, $this->repository->getError());
    }

    /** @test */
    public function it_sets_an_error_on_exceeding_max_filesize_exceptions()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $error = 'Max filesize exceeded.';

        $this->app['translator']->shouldReceive('trans')
            ->with('platform/media::message.file_size_exceeded', [], 'messages', '')
            ->once()
            ->andReturn($error);

        $this->app['cartalyst.filesystem']->shouldReceive('validateFile')
            ->once()
            ->andThrow(new \Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException);

        $this->repository->validForUpload($file);

        $this->assertEquals($error, $this->repository->getError());
    }

    /** @test */
    public function it_sets_an_error_on_invalid_mimetype_exceptions()
    {
        $file = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $error = 'Invalid mimetype.';

        $this->app['translator']->shouldReceive('trans')
            ->with('platform/media::message.invalid_mime', [], 'messages', '')
            ->once()
            ->andReturn($error);

        $this->app['cartalyst.filesystem']->shouldReceive('validateFile')
            ->once()
            ->andThrow(new \Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException);

        $this->repository->validForUpload($file);

        $this->assertEquals($error, $this->repository->getError());
    }

    /** @test */
    public function it_can_validate_for_update()
    {
        $data = ['slug' => 'foo', 'uri' => 'foo'];

        $model = m::mock('Platform\Media\Models\Media');

        $this->app['platform.media.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.media.validator']);

        $this->app['platform.media.validator']->shouldReceive('validate')
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->repository->validForUpdate($model, $data));
    }

    /** @test */
    public function it_can_create()
    {
        $data = ['slug' => 'foo'];

        $model = $this->shouldReceiveCreate(false);
        $model->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($model);

        $media = $this->repository->create($data);

        $this->assertInstanceOf('Platform\Media\Models\Media', $media);
    }

    /** @test */
    public function it_can_upload()
    {
        $uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $uploaded->shouldReceive('getClientOriginalName')
            ->twice();

        $file = m::mock('Cartalyst\Filesystem\File');

        $data = [
            'name' => 'Foo',
        ];

        $preparedData = [
            'name'      => 'Foo',
            'path'      => 'foo_path',
            'extension' => 'png',
            'mime'      => null,
            'size'      => null,
            'is_image'  => true,
            'width'     => 1,
            'height'    => 1,
        ];

        $this->app['cartalyst.filesystem']->shouldReceive('upload')
            ->with($uploaded, 'foo_1.')
            ->once()
            ->andReturn($file);

        $file->shouldReceive('getPath')
            ->once()
            ->andReturn('foo_path');

        $file->shouldReceive('getImageSize')
            ->once()
            ->andReturn(['width' => 1, 'height' => 1]);

        $file->shouldReceive('getExtension')
            ->once()
            ->andReturn('png');

        $file->shouldReceive('getMimetype')
            ->once();

        $file->shouldReceive('getSize')
            ->once();

        $file->shouldReceive('isImage')
            ->once()
            ->andReturn(true);

        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('fill')
            ->with($preparedData)
            ->once()
            ->andReturn($model);

        $model->shouldReceive('save')
            ->twice();

        $model->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $this->app['platform.tags']->shouldReceive('set')
            ->with($model, null)
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.uploaded', [ $model, $file, $uploaded ])
            ->once();

        $this->repository->upload($uploaded, $data);
    }

    /** @test */
    public function it_can_update()
    {
        $uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');
        $uploaded->shouldReceive('getClientOriginalName')
            ->once();

        $file = m::mock('Cartalyst\Filesystem\File');

        $data = [
            'name' => 'Foo',
        ];

        $preparedData = [
            'name'      => 'Foo',
            'path'      => 'foo_path',
            'extension' => null,
            'mime'      => null,
            'size'      => null,
            'is_image'  => true,
            'width'     => 1,
            'height'    => 1,
        ];

        $this->app['cartalyst.filesystem']->shouldReceive('validateFile')
            ->once()
            ->andReturn(true);

        $this->app['cartalyst.filesystem']->shouldReceive('delete')
            ->once();

        $this->app['cartalyst.filesystem']->shouldReceive('upload')
            ->with($uploaded, 'foo')
            ->once()
            ->andReturn($file);

        $file->shouldReceive('getPath')
            ->once()
            ->andReturn('foo_path');

        $file->shouldReceive('getImageSize')
            ->once()
            ->andReturn(['width' => 1, 'height' => 1]);

        $file->shouldReceive('getExtension')
            ->once();

        $file->shouldReceive('getMimetype')
            ->once();

        $file->shouldReceive('getSize')
            ->once();

        $file->shouldReceive('isImage')
            ->once()
            ->andReturn(true);

        $model = m::mock('Platform\Media\Models\Media');

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('path')
            ->andReturn('foo');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.1', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($model);

        $model->shouldReceive('find')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('fill')
            ->with($preparedData)
            ->once()
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();

        $this->app['platform.tags']->shouldReceive('set')
            ->with($model, null)
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.uploaded', [ $model, $file, $uploaded ])
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.updating', [ $model ])
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.updated', [ $model ])
            ->once();

        $this->repository->update(1, $data, $uploaded);
    }

    /** @test */
    public function it_returns_false_on_invalid_update()
    {
        $uploaded = m::mock('Symfony\Component\HttpFoundation\File\UploadedFile');

        $file = m::mock('Cartalyst\Filesystem\File');

        $error = 'error message';

        $this->app['cartalyst.filesystem']->shouldReceive('validateFile')
            ->once()
            ->andThrow(new \Cartalyst\Filesystem\Exceptions\InvalidFileException);

        $this->app['translator']->shouldReceive('trans')
                ->once()
                ->andReturn($error);

        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.1', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($model);

        $model->shouldReceive('find')
            ->once()
            ->andReturn($model);

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.updating', [ $model ])
            ->once();

        $this->assertFalse($this->repository->update(1, [], $uploaded));
    }

    /** @test */
    public function it_can_delete()
    {
        $file = m::mock('Cartalyst\Filesystem\File');

        $this->app['cartalyst.filesystem']->shouldReceive('get')
            ->once()
            ->andReturn($file);

        $this->app['cartalyst.filesystem']->shouldReceive('delete')
            ->once();

        $model = m::mock('Platform\Media\Models\Media');

        $model->shouldReceive('getAttribute')
            ->twice()
            ->with('path')
            ->andReturn('foo');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.1', m::on(function ($callback) {
                $callback();
                return true;
            }))->andReturn($model);

        $model->shouldReceive('find')
            ->once()
            ->andReturn($model);

        $model->shouldReceive('relations')
            ->once()
            ->andReturn($collection = m::mock('Illuminate\Support\Collection'));

        $collection->shouldReceive('delete')
            ->once();

        $model->shouldReceive('delete')
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.deleting', [ $model, $file ])
            ->once();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.deleted', [ $model ])
            ->once();

        $this->repository->delete(1);
    }

    /** @test */
    public function it_sets_an_error_when_deleting_non_existing_media()
    {
        $model = m::mock('Platform\Media\Models\Media');

        $error = 'error message';

        $this->app['translator']->shouldReceive('trans')
            ->once()
            ->andReturn($error);

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        $this->app['cache']->shouldReceive('rememberForever')
            ->once()
            ->with('platform.media.1', m::on(function ($callback) {
                $callback();
                return true;
            }));

        $model->shouldReceive('find')
            ->once();

        $this->repository->delete(1);

        $this->assertEquals($error, $this->repository->getError());
    }

    /**
     * Repository should receive createModel.
     *
     * @param  bool  $withTags
     * @return mixed
     */
    protected function shouldReceiveCreateModel($withTags = true)
    {
        $model = m::mock('Platform\Media\Models\Media');

        $this->repository->shouldReceive('createModel')
            ->once()
            ->andReturn($model);

        if ($withTags) {
            $model->shouldReceive('with')
                ->with('tags')
                ->once()
                ->andReturn($model);
        }

        return $model;
    }

    /**
     * Media creation expectations.
     *
     * @param  arary  $data
     * @return \Platform\Media\Models\Media
     */
    protected function shouldReceiveCreate($data)
    {
        $model = $this->shouldReceiveCreateModel(false);

        return $model;
    }

    /**
     * Media update expectations.
     *
     * @param  arary  $data
     * @return void
     */
    protected function shouldReceiveUpdate($data)
    {
        $model = $this->shouldReceiveFind();

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.updating', [ $model, $data ])
            ->once();

        $this->app['platform.media.handler.data']->shouldReceive('prepare')
            ->once()
            ->andReturn($data);

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('slug')
            ->andReturn('foo');

        $model->shouldReceive('getAttribute')
            ->once()
            ->with('uri')
            ->andReturn('foo');

        $this->app['events']->shouldReceive('fire')
            ->with('platform.media.updated', [ $model ])
            ->once();

        $this->app['platform.media.validator']->shouldReceive('on')
            ->with('update')
            ->once()
            ->andReturn($this->app['platform.media.validator']);

        $this->app['platform.media.validator']->shouldReceive('bind')
            ->with($data)
            ->once()
            ->andReturn($this->app['platform.media.validator']);

        $this->app['platform.media.validator']->shouldReceive('validate')
            ->once()
            ->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

        $messages->shouldReceive('isEmpty')
            ->once()
            ->andReturn(true);

        $model->shouldReceive('fill')
            ->once()
            ->with($data)
            ->andReturn($model);

        $model->shouldReceive('save')
            ->once();
    }
}
