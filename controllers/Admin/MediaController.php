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

use Platform\Routing\Controllers\AdminController;

class MediaController extends AdminController {

	/**
	 * Content management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-media');

		try
		{
			// Get the media list
			$request = \API::get('media', array(
				'limit' => 10
			));
			$media = $request['media'];
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the admin dashboard
			return \Redirect::to(ADMIN_URI);
		}

		// Show the page
		return \View::make('platform/media::index', compact('media'));
	}

	/**
	 *
	 *
	 * @param  int  $mediaId
	 * @return mixed
	 */
	public function getView($mediaId = null)
	{
		try
		{
			// Get the media information
			$request = \API::get('media/' . $mediaId);
			$media   = $request['media'];
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the media management page
			return \Redirect::to(ADMIN_URI . '/media');
		}

		// Show the page
		return \View::make('platform/media::view', compact('media'));
	}

	/**
	 * Media upload.
	 *
	 * @return View
	 */
	public function getUpload()
	{
		// Show the page
		return \View::make('platform/media::upload');
	}

	# API::post('media', $file);
	public function postUpload()
	{
		//
		$mediaPath = media_storage_directory(); # or create a method in the model -> Media::getDirectory();

		$file = \Input::file('files');

		$newPath = $mediaPath . $file->getClientOriginalName();

		// Move the uploaded file to public/media directory
		$file->move($mediaPath, $newPath);

		$info = array(
			'name'           => $file->getClientOriginalName(),
			'file_path'      => '', # this will be stored on the media folder, for now!
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

		echo '<pre>';
		print_r($info);
	}


	/**
	 * Media delete.
	 *
	 * @param  int  $mediaId
	 * @return Redirect
	 */
	public function getDelete($mediaId = null)
	{
		try
		{
			// Delete the media
			\API::delete('media/' . $mediaId);

			// Set the success message
			# TODO !
		}
		catch (\Cartalyst\Api\ApiHttpException $e)
		{
			// Set the error message
			# TODO !
		}

		// Redirect to the media management page
		return \Redirect::to(ADMIN_URI . '/media');
	}

}
