<?php namespace Platform\Media\Controllers\Frontend;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Media;
use Platform\Foundation\Controllers\BaseController;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Response;
use Sentry;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MediaController extends BaseController {

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
	 * Returns the given media file.
	 *
	 * @param  string  $path
	 * @return \Illuminate\Http\Response
	 */
	public function view($path)
	{
		$media = $this->getMedia($path);

		$file = Media::getFileSystem()->read($media->path);

		$headers = [
			'Content-Type' => $media->mime,
		];

		return $this->respond($file, $headers);
	}

	/**
	 * Downloads the given media file.
	 *
	 * @param  string  $path
	 * @return \Illuminate\Http\Response
	 */
	public function download($path)
	{
		$media = $this->getMedia($path);

		$file = Media::getFileSystem()->read($media->path);

		$headers = [
			'Connection'          => 'close',
			'Content-Disposition' => 'attachment; filename="'.$media->name.'"',
			'Content-Length'      => strlen($file),
			'Content-Type'        => $media->mime,
		];

		return $this->respond($file, $headers);
	}

	/**
	 * Grabs the media file by its path and determines if the
	 * logged in user  has access to the media file.
	 *
	 * @param  string  $path
	 * @return \Platform\Media\Media
	 */
	protected function getMedia($path)
	{
		if ( ! $media = $this->media->findByPath($path))
		{
			throw new HttpException(404, 'Media does not exist.');
		}

		if ($media->private)
		{
			$pass = false;

			if ($user = Sentry::check())
			{
				$pass = true;

				$mediaGroups = $media->groups;

				$userGroups = $user->groups->lists('id');

				if ( ! empty($mediaGroups) and ! array_intersect($mediaGroups, $userGroups))
				{
					$pass = false;
				}
			}

			if ( ! $pass)
			{
				throw new HttpException(403, "You don't have permission to access this file.");
			}
		}

		return $media;
	}

	/**
	 * Sends the response with the appropriate headers.
	 *
	 * @param  string  $file
	 * @param  array  $headers
	 * @return \Illuminate\Http\Response
	 */
	protected function respond($file, $headers = [])
	{
		$response = Response::make($file, 200);

		foreach ($headers as $name => $value)
		{
			$response->header($name, $value);
		}

		return $response;
	}

}
