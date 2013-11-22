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
use DataGrid;
use Illuminate\Support\MessageBag as Bag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use Response;
use View;

class MediaController extends AdminController {

	/**
	 * Content management main page.
	 *
	 * @return \View
	 */
	public function getIndex()
	{
		// Show the page
		return View::make('platform/media::index');
	}

	/**
	 * Datasource for the media Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		// Get the all the media
		$response = API::get('v1/media');

		// Return the Data Grid object
		return DataGrid::make($response['media'], array(
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
	public function postUpload()
	{
		try
		{
			API::post('v1/media');

			return Response::json('success');
		}
		catch (ApiHttpException $e)
		{
			return Response::json($e->getMessage(), 400);
		}
	}

	/**
	 * Remove the specified media.
	 *
	 * @param  int  $id
	 * @return \Redirect
	 */
	public function getDelete($id = null)
	{
		try
		{
			// Delete the media
			API::delete("v1/media/{$id}");

			// Set the success message
			$bag = with(new Bag)->add('success', Lang::get('platform/media::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$bag = with(new Bag)->add('error', Lang::get('platform/media::message.error.delete'));
		}

		// Redirect to the media management page
		return Redirect::toAdmin('media')->withNotifications($bag);
	}

}
