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

use DataGrid;
use Input;
use Lang;
use Filesystem;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Platform\Users\Repositories\RoleRepositoryInterface;
use Platform\Users\Repositories\UserRepositoryInterface;
use Redirect;
use Request;
use Response;
use View;

class MediaController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
		//'update',
	];

	/**
	 * Media repository.
	 *
	 * @var \Platform\Media\Repositories\MediaRepositoryInterface
	 */
	protected $media;

	/**
	 * The Users repository.
	 *
	 * @var \Platform\Users\Repositories\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * The Users Roles repository.
	 *
	 * @var \Platform\Users\Repositories\RoleRepositoryInterface
	 */
	protected $roles;

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
	 * @param  \Platform\Users\Repositories\UserRepositoryInterface  $users
	 * @param  \Platform\Users\Repositories\RoleRepositoryInterface  $roles
	 * @return void
	 */
	public function __construct(
		MediaRepositoryInterface $media,
		UserRepositoryInterface $users,
		RoleRepositoryInterface $roles
	)
	{
		parent::__construct();

		$this->media = $media;

		$this->users = $users;

		$this->roles = $roles;
	}

	/**
	 * Display a listing of media files.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		// Get a list of all the available tags
		$tags = $this->media->getTags();

		// Get a list of all the available roles
		$roles = $this->roles->findAll();

		// Show the page
		return View::make('platform/media::index', compact('tags', 'roles'));
	}

	/**
	 * Datasource for the media Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->media->grid();

		$columns = [
			'id',
			'tags',
			'name',
			'mime',
			'path',
			'size',
			'private',
			//'roles',
			'is_image',
			//'extension',
			'thumbnail',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return DataGrid::make($data, $columns, $settings);
	}

	/**
	 * Media upload form processing.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function upload()
	{
		$file = Input::file('file');

		$tags = Input::get('tags', []);

		if ($this->media->validForUpload($file))
		{
			if ($media = $this->media->upload($file, $tags))
			{
				return Response::json($media);
			}
		}

		return Response::json($this->media->getError(), 400);
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
			$message = Lang::get('platform/media::message.not_found', compact('id'));

			return Redirect::toAdmin('media')->withErrors($message);
		}

		// Get a list of all the available tags
		$tags = $this->media->getTags();

		// Get a list of all the available roles
		$roles = $this->roles->findAll();

		// Show the page
		return View::make('platform/media::form', compact('media', 'tags', 'roles'));
	}

	/**
	 * Processes the form for updating a media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id)
	{
		Input::merge(['roles' => Input::get('roles', [])]);

		Input::merge(['tags' => Input::get('tags', [])]);

		$input = Input::except('file');

		if ($this->media->validForUpdate($id, $input))
		{
			if ($this->media->update($id, $input, Input::file('file')))
			{
				if (Request::ajax())
				{
					return Response::json('success');
				}

				$message = Lang::get('platform/media::message.success.update');

				return Redirect::toAdmin('media')->withSuccess($message);
			}
		}

		if (Request::ajax())
		{
			return Response::json($this->media->getError(), 400);
		}

		return Redirect::back()->withErrors($this->media->getError());
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = Input::get('action');

		if (in_array($action, $this->actions))
		{
			foreach (Input::get('entries', []) as $entry)
			{
				$this->media->{$action}($entry);
			}

			return Response::json('Success');
		}

		return Response::json('Failed', 500);
	}

}
