<?php namespace Platform\Media\Controllers\Api\V1;
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

use Platform\Routing\Controllers\ApiController;
use Platform\Media\Media;

use Platform\Media\Manager;

class MediaController extends ApiController {

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $validationRules = array(

	);

	/**
	 *
	 *
	 * @var Platform\Media\Media
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$app = app();

		$this->model = $app->make('platform/media::media')->newQuery();
	}

	/**
	 * Display a listing of media using the given filters.
	 *
	 * @return Cartalyst\Api\Http\Response
	 * @todo   Refactor to allow search filters !!
	 */
	public function index()
	{
		/*
		$manager = new Manager;

		echo $manager->directory();

		die;
		*/

		if ( ! $limit = $this->input('limit'))
		{
			return $this->response(array('media' => $this->model->all()));
		}

		return $this->response(array('media' => $this->model->paginate($limit)));
	}

	/**
	 * Uploads a new media file.
	 *
	 * @return Cartalyst\Api\Http\Response
	 * @todo   Implement validation
	 */
	public function create()
	{
		if (\Input::hasFile('files'))
		{
			//
			$file = \Input::file('files');

			//
			$mediaPath = media_storage_directory(); # maybe change this, later, not sure!

			//
			$newPath = $mediaPath . $file->getClientOriginalName();

			// Move the uploaded file to the media storage directory
			if ($file->move($mediaPath, $newPath))
			{
				//
				$info = array(
					'name'           => $file->getClientOriginalName(),
					'file_path'      => '', # this will be stored on the media folder root, for now!
					'file_name'      => $file->getClientOriginalName(),
					'file_extension' => $file->getClientOriginalExtension(),
					'file_mime_type' => $file->getClientMimeType(),
					'file_size'      => $file->getClientSize()
				);

				// Validate image
				if (in_array($file->getClientMimeType(), array('image/jpeg', 'image/png', 'image/gif', 'image/bmp')) and $size = getimagesize($newPath))
				{
					$info['width']  = $size[0];
					$info['height'] = $size[1];
				}


				$media = new Media($info);

				if($media->save())
				{
					//
					return $this->response('');
				}
			}

			# we are because something went wrong :c

			# echo '<pre>';
			# print_r($info);
			return $this->response(array(
				'message' => 'something went wrong here...'
			), 500);
		}
	}

	/**
	 * Returns information about the given media.
	 *
	 * @param  int  $mediaId
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($mediaId)
	{
		// Do we have the media slug?
		if ( ! is_numeric($mediaId))
		{
			$media = $this->model->where('slug', '=', $mediaId);
		}

		// We must have the media id
		else
		{
			$media = $this->model->where('id', '=', $mediaId);
		}

		// Check if the media exists
		if ( ! is_null($media = $media->first()))
		{
			return $this->response(compact('media'));
		}

		// Content does not exist
		return $this->response(array(
			'message' => \Lang::get('platform/media::messages.does_not_exist', compact('mediaId'))
		), 404);
	}




	/**
	 * Deletes the given media.
	 *
	 * @param  int  $mediaId
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($mediaId)
	{
		// Check if the media exists
		if (is_null($media = $this->model->find($mediaId)))
		{
			return $this->response(array(
				'message' => \Lang::get('platform/media::messages.does_not_exist', compact('mediaId'))
			), 404);
		}

		// Was the media deleted?
		if ($media->delete())
		{
			return $this->response(array(
				'message' => \Lang::get('platform/media::messages.delete.success')
			));
		}

		// There was a problem deleting the media
		return $this->response(array(
			'message' => \Lang::get('platform/media::messages.delete.error')
		), 500);
	}

}
