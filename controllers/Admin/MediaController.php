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

use API;
use Cartalyst\Api\Http\ApiHttpException;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use View;

class MediaController extends AdminController {

	/**
	 * Content management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		try
		{
			// Set the current active menu
			set_active_menu('admin-media');

			// Get the media list
			$response = API::get('v1/media', array();
			$media    = $response['media'];

			// Show the page
			return View::make('platform/media::index', compact('media'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the admin dashboard
			return Redirect::toAdmin('/');
		}
	}

	/**
	 *
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getView($id = null)
	{
		try
		{
			// Get the media information
			$response = API::get("media/$id");
			$media    = $response['media'];

			// Show the page
			return View::make('platform/media::view', compact('media'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the media management page
			return Redirect::toAdmin('media');
		}
	}

	/**
	 * Media upload.
	 *
	 * @return View
	 */
	public function getUpload()
	{
		// Show the page
		return View::make('platform/media::upload');
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
			API::post('media', array('files' => \Input::file()));

			// Set the success message
			# TODO !
		}
		catch (ApiHttpException $e)
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
	 * @param  int  $id
	 * @return Redirect
	 */
	public function getDelete($id = null)
	{
		try
		{
			// Delete the media
			API::delete("media/$id");

			// Set the success message
			$notifications = with(new Bag)->add('success', Lang::get('platform/media::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', Lang::get('platform/media::message.error.delete'));
		}

		// Redirect to the media management page
		return Redirect::toAdmin('media')->with('notifications', $notifications);
	}

}
