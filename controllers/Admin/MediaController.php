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

	/**
	 * Media upload form processing.
	 *
	 * @return Redirect
	 */
	public function postUpload()
	{
		try
		{
			// Upload the file
			\API::post('media', array('files' => \Input::file()));

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
