<?php namespace Platform\Media\Controllers\Admin;
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Access\Controllers\AdminController;
use Platform\Tags\Repositories\TagsRepositoryInterface;
use Platform\Roles\Repositories\RoleRepositoryInterface;
use Platform\Media\Repositories\MediaRepositoryInterface;

class MediaController extends AdminController {

	/**
	 * The Media repository.
	 *
	 * @var \Platform\Media\Repositories\MediaRepositoryInterface
	 */
	protected $media;

	/**
	 * The Users Roles repository.
	 *
	 * @var \Platform\Users\Repositories\RoleRepositoryInterface
	 */
	protected $roles;

	/**
	 * The Tags repository.
	 *
	 * @var \Platform\Tags\Repositories\TagsRepositoryInterface
	 */
	protected $tags;

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
	];

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @param  \Platform\Users\Repositories\RoleRepositoryInterface  $roles
	 * @param  \Platform\Tags\Repositories\TagsRepositoryInterface  $tags
	 * @return void
	 */
	public function __construct(
		MediaRepositoryInterface $media,
		RoleRepositoryInterface $roles,
		TagsRepositoryInterface $tags
	)
	{
		parent::__construct();

		$this->media = $media;

		$this->roles = $roles;

		$this->tags = $tags;
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

		// Show the page
		return view('platform/media::index', compact('tags', 'roles'));
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
			'thumbnail',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return datagrid($this->media->grid(), $columns, $settings);
	}

	/**
	 * Media upload form processing.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function upload()
	{
		$file = request()->file('file');

		if ($this->media->validForUpload($file))
		{
			if ($media = $this->media->upload($file, request()->input()))
			{
				return response($media);
			}
		}

		return response($this->media->getError(), 400);
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
		if ( ! $media = $this->media->find($id))
		{
			$this->alerts->error(trans('platform/media::message.not_found', compact('id')));

			return redirect()->toAdmin('media');
		}

		// Get a list of all the available tags
		$tags = $this->media->getAllTags();

		// Get a list of all the available roles
		$roles = $this->roles->findAll();

		// Show the page
		return view('platform/media::form', compact('media', 'tags', 'roles'));
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

		if ($this->media->validForUpdate($id, $input))
		{
			if ($this->media->update($id, $input, request()->file('file')))
			{
				if (request()->ajax())
				{
					return response(
						trans('platform/media::message.success.update')
					);
				}

				$this->alerts->success(trans('platform/media::message.success.update'));

				return redirect()->toAdmin('media');
			}
		}

		if (request()->ajax())
		{
			return response($this->media->getError(), 400);
		}

		$this->alerts->error($this->media->getError());

		return redirect()->back();
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = request()->input('action');

		if (in_array($action, $this->actions))
		{
			foreach (request()->input('entries', []) as $entry)
			{
				$this->media->{$action}($entry);
			}

			return response('Success');
		}

		return response('Failed', 500);
	}

}
