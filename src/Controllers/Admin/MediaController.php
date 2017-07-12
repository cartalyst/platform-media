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
 * @version    4.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Controllers\Admin;

use Platform\Access\Controllers\AdminController;
use Platform\Tags\Repositories\TagsRepositoryInterface;
use Platform\Roles\Repositories\RoleRepositoryInterface;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Platform\Attributes\Repositories\ManagerRepositoryInterface;

class MediaController extends AdminController
{
    /**
     * The Media repository.
     *
     * @var \Platform\Media\Repositories\MediaRepositoryInterface
     */
    protected $media;

    /**
     * The Users Roles repository.
     *
     * @var \Platform\Roles\Repositories\RoleRepositoryInterface
     */
    protected $roles;

    /**
     * The Tags repository.
     *
     * @var \Platform\Tags\Repositories\TagsRepositoryInterface
     */
    protected $tags;

    /**
     * The Namespaces repository.
     *
     * @var \Platform\Attributes\Repositories\ManagerRepositoryInterface
     */
    protected $namespaces;

    /**
     * Holds all the mass actions we can execute.
     *
     * @var array
     */
    protected $actions = [
        'delete',
        'makePrivate',
        'makePublic',
    ];

    /**
     * Constructor.
     *
     * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
     * @param  \Platform\Roles\Repositories\RoleRepositoryInterface  $roles
     * @param  \Platform\Tags\Repositories\TagsRepositoryInterface  $tags
     * @param  \Platform\Attributes\Repositories\ManagerRepositoryInterface  $namespaces
     * @return void
     */
    public function __construct(
        MediaRepositoryInterface $media,
        RoleRepositoryInterface $roles,
        TagsRepositoryInterface $tags,
        ManagerRepositoryInterface $namespaces
    ) {
        parent::__construct();

        $this->media = $media;

        $this->roles = $roles;

        $this->tags = $tags;

        $this->namespaces = $namespaces;
    }

    /**
     * Display a listing of media files.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get a list of all the available tags
        $tags = $this->media->getAllTags();

        // Get a list of all the available roles
        $roles = $this->roles->findAll();

        // Get a list of all the allowed mime types
        $allowedMimes = $this->media->getAllowedMimes();

        // Prepare mimes
        $mimes = $this->prepareMimes($allowedMimes);

        // Show the page
        return view('platform/media::index', compact('tags', 'roles', 'mimes'));
    }

    /**
     * Datasource for the media Data Grid.
     *
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function grid()
    {
        $columns = [
            'id',
            'name',
            'mime',
            'path',
            'size',
            'private',
            'is_image',
            'width',
            'height',
            'created_at',
        ];

        $settings = [
            'sort'      => 'created_at',
            'direction' => 'desc',
            'pdf_view'  => 'pdf',
        ];

        $transformer = function ($element) {
            $element->thumbnail_uri = getImagePath($element, 'thumb');
            $element->view_uri      = route('media.view', $element->path);
            $element->edit_uri      = route('admin.media.edit', $element->id);
            $element->email_uri     = route('admin.media.email', $element->id);
            $element->download_uri  = route('media.download', $element->path);

            return $element;
        };

        return datagrid($this->media->grid(), $columns, $settings, $transformer);
    }

    /**
     * Media files list.
     *
     * @return string
     */
    public function filesList()
    {
        $columns = [
            'name',
            'path' => 'link',
            'size',
        ];

        $settings = [
            'sort'      => 'created_at',
            'direction' => 'desc',
        ];

        $transformer = function ($media) {
            return [
                'title' => $media->name,
                'name'  => $media->name,
                'link'  => route('media.download', $media->link),
                'size'  => formatBytes($media->size),
            ];
        };

        return datagrid($this->media->grid(), $columns, $settings, $transformer)->getDataHandler()->getResults();
    }

    /**
     * Media images list.
     *
     * @return string
     */
    public function imagesList()
    {
        $columns = [
            'name',
            'path',
        ];

        $settings = [
            'sort'      => 'created_at',
            'direction' => 'desc',
        ];

        $transformer = function ($media) {
            return [
                'thumb' => getImagePath($media, 'thumb'),
                'image' => route('media.view', $media->path),
                'title' => $media->name,
            ];
        };

        $data = $this->media->grid()->where('is_image', true);

        return datagrid($data, $columns, $settings, $transformer)->getDataHandler()->getResults();
    }

    /**
     * Media upload form processing.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        $file = request()->file('file');

        if ($this->media->validForUpload($file)) {
            if ($media = $this->media->upload($file, request()->input())) {
                return response($media);
            }
        }

        return response($this->media->getError(), 400);
    }

    /**
     * Media upload form processing for redactor.
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadRedactor()
    {
        $media = json_decode($this->upload()->content());

        return response([
            'filelink' => route('media.view', $media->path),
        ]);
    }

    /**
     * Associates media records to objects.
     *
     * @return string
     */
    public function linkMedia()
    {
        $class = request()->input('object_class');
        $model = (new $class())->find(request()->input('model_id'));

        if ($mediaIds = request()->input('_new_media_ids')) {
            $mediaIds         = is_array($mediaIds) ? $mediaIds : json_decode($mediaIds);
            $preparedMediaIds = [];
            foreach ($mediaIds as $key => $id) {
                $preparedMediaIds[$id] = ['sort' => $key];
            }

            $model->media()->sync($preparedMediaIds);

            return response([
                'success' => true,
            ]);
        }

        return response([
            'error' => $this->media->getError(),
        ]);
    }

    /**
     * Shows the form for updating a media.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        // Get the media information
        if (! $media = $this->media->find($id)) {
            $this->alerts->error(trans('platform/media::message.not_found', compact('id')));

            return redirect()->route('admin.media.all');
        }

        // Get a list of all the available namespaces
        $namespaces = $this->namespaces->getNamespaces();

        // Get a list of all the available tags
        $tags = $this->media->getAllTags();

        // Get a list of all the available roles
        $roles = $this->roles->findAll();

        // Show the page
        return view('platform/media::form', compact('media', 'namespaces', 'tags', 'roles'));
    }

    /**
     * Processes the form for updating a media.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $input = request()->except('file');

        if ($this->media->validForUpdate($id, $input)) {
            if ($this->media->update($id, $input, request()->file('file'))) {
                if (request()->ajax()) {
                    return response(
                        trans('platform/media::message.success.update')
                    );
                }

                $this->alerts->success(trans('platform/media::message.success.update'));

                return redirect()->route('admin.media.all');
            }
        }

        if (request()->ajax()) {
            return response($this->media->getError(), 400);
        }

        $this->alerts->error($this->media->getError());

        return redirect()->back();
    }

    /**
     * Removes the specified media.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $type = $this->media->delete($id) ? 'success' : 'error';

        $this->alerts->{$type}(
            trans("platform/media::message.{$type}.delete")
        );

        return redirect()->route('admin.media.all');
    }

    /**
     * Executes the mass action.
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAction()
    {
        $action = request()->input('action');

        if (in_array($action, $this->actions)) {
            foreach (request()->input('rows', []) as $entry) {
                $this->media->{$action}($entry);
            }

            return response('Success');
        }

        return response('Failed', 500);
    }

    /**
     * Prepares mime types for output.
     *
     * @param  array  $mimes
     * @return string
     */
    protected function prepareMimes($mimes)
    {
        $mimes = array_map(function ($el) {
            return last(explode('/', $el));
        }, $mimes);

        return implode(', ', $mimes);
    }
}
