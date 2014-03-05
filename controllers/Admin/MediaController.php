<?php namespace Platform\Media\Controllers\Admin;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use DataGrid;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Redirect;
use Response;
use Request;
use Sentry;
use View;

class MediaController extends AdminController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = array(
		'update',
		'delete',
	);

	/**
	 * Media repository.
	 *
	 * @var \Platform\Media\Repositories\MediaRepositoryInterface
	 */
	protected $media;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @return void
	 */
	public function __construct(MediaRepositoryInterface $media)
	{
		parent::__construct();

		$this->media = $media;
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

		// Get a list of all the available groups
		$groups = Sentry::getGroupRepository()->createModel()->all();

		// Show the page
		return View::make('platform/media::index', compact('tags', 'groups'));
	}

	/**
	 * Datasource for the media Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		return DataGrid::make($this->media->grid(), array(
			'id',
			'name',
			'mime',
			'path',
			'size',
			'private',
			'groups',
			'is_image',
			'extension',
			'thumbnail',
			'created_at',
		));
	}

	/**
	 * Media upload form processing.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function upload()
	{
		$file = Input::file('file');

		$tags = Input::get('tags', array());

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
			return Redirect::toAdmin('media')->withErrors(Lang::get('platform/media::message.not_found', compact('id')));
		}

		// Get a list of all the available tags
		$tags = $this->media->getTags();

		// Get a list of all the available groups
		$groups = Sentry::getGroupRepository()->createModel()->all();

		// Show the page
		return View::make('platform/media::form', compact('media', 'tags', 'groups'));
	}

	/**
	 * Processes the form for updating a media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id)
	{
		Input::merge(array('groups' => Input::get('groups', array())));

		$input = Input::except('file');

		if ($this->media->validForUpdate($id, $input))
		{
			$this->media->update($id, $input, Input::file('file'));

			if (Request::ajax())
			{
				return Response::json('success');
			}

			return Redirect::toAdmin('media')->withSuccess(Lang::get('platform/media::message.success.update'));
		}

		if (Request::ajax())
		{
			return Response::json($this->media->getError(), 400);
		}

		return Redirect::back()->withErrors($this->media->getError());
	}

	/**
	 * Remove the specified media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function delete($id = null)
	{
		// Delete the media
		if ($this->media->delete($id))
		{
			return Response::json('success');
		}

		return Response::json($this->media->getError(), 400);
	}

}
