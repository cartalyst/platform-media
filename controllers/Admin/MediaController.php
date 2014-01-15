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
use Sentry;
use View;

class MediaController extends AdminController {

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
		// Show the page
		return View::make('platform/media::index');
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
			'created_at',
		));
	}

	/**
	 * Media upload form processing.
	 *
	 * @return mixed
	 */
	public function upload()
	{
		$file = Input::file('file');

		if ($this->media->validForUpload($file))
		{
			if ($this->media->upload($file))
			{
				return Response::json('success');
			}
		}

		return Response::json($this->media->getError(), 400);
	}

	/**
	 * Shows the form for updating a media.
	 *
	 * @param  int $id
	 * @return mixed
	 */
	public function edit($id)
	{
		// Get the media information
		if ( ! $media = $this->media->find($id))
		{
			return Redirect::toAdmin('media')->withErrors(Lang::get('platform/media::message.not_found', compact('id')));
		}

		// Get a list of all the available groups
		$groups = Sentry::getGroupRepository()->createModel()->all();

		// Show the page
		return View::make('platform/media::form', compact('media', 'groups'));
	}

	/**
	 * Processes the form for updating a media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		// Get the input data
		$input = Input::all();

		// Check if the input is valid
		$messages = $this->media->validForUpdate($id, $input);

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Update the media
			$media = $this->media->update($id, $input);
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get('platform/media::message.success.update');

			return Redirect::toAdmin("media/{$media->id}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->withErrors($messages);
	}

	/**
	 * Remove the specified media.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
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

	/**
	 * Deletes the given media files.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function massDelete()
	{
		$deleted = 0;

		foreach (Input::get('media', array()) as $id)
		{
			if ($item = $this->media->delete($id))
			{
				$deleted += 1;
			}
		}

		if ($deleted > 0)
		{
			return Redirect::toAdmin('media')->withSuccess(Lang::choice('platform/media::message.error.multiple', $deleted, array('items' => $deleted)));
		}

		return Redirect::toAdmin('media');
	}

}
