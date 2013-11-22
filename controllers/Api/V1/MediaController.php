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

use Config;
use Input;
use Platform\Routing\Controllers\ApiController;
use Response;

use Media;

class MediaController extends ApiController {

	/**
	 * Holds the media model.
	 *
	 * @var \Platform\Media\Models\Media
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->model = app('Platform\Media\Models\Media');
	}

	/**
	 * Display a listing of media using the given filters.
	 *
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		$query = $this->model->newQuery();

		if ($limit = Input::get('limit'))
		{
			$media = $query->paginate($limit);
		}
		else
		{
			$media = $query->get();
		}

		return Response::api(compact('media'));
	}

	/**
	 * Uploads a new media file.
	 *
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		try
		{
			$dispersion = Config::get('platform/media::dispersion');

			$file = Input::file('file');

			$data = Media::setDispersion($dispersion)->upload($file->getPathName(), $file->getClientOriginalName());

			$imageSize = $data->getImageSize();

			$this->model->create(array(
				'name'      => $file->getClientOriginalName(),
				'path'      => $data->getPath(),
				'extension' => $data->getExtension(),
				'mime'      => $data->getMimetype(),
				'size'      => $data->getSize(),
				'width'     => $imageSize['width'],
				'height'    => $imageSize['height']
			));

			return Response::api('success');
		}
		catch (\Flysystem\FileExistsException $e)
		{
			return Response::api('File already exists.', 400);
		}
	}

	/**
	 * Deletes the given media.
	 *
	 * @param  int  $mediaId
	 * @return \Cartalyst\Api\Http\Response
	 */
	public function destroy($mediaId)
	{
		// Check if the media exists
		if (is_null($media = $this->model->find($mediaId)))
		{
			return Response::api(Lang::get('platform/media::messages.does_not_exist', compact('mediaId')), 404);
		}

		Media::delete($media->file_path);

		// Was the media deleted?
		if ($media->delete())
		{
			return Response::api(Lang::get('platform/media::messages.delete.success'));
		}

		// There was a problem deleting the media
		return Response::api(Lang::get('platform/media::messages.delete.error'), 500);
	}

}
