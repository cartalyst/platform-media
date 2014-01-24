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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Exception;
use Image;
use Input;
use Media;
use Platform\Foundation\Controllers\BaseController;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Response;
use Sentry;

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
	 * Return the given media file.
	 *
	 * @param  string  $id
	 * @param  string  $size
	 * @return void
	 * @TODO: Check if the media is private and check the groups
	 * @TODO: Rework the exception and throw proper responses with proper status
	 */
	public function view($id, $size = null)
	{
		if ( ! $media = $this->media->findByUniqueId($id))
		{
			throw new Exception('Not found');
		}

		$file = Media::getFileSystem()->read($media->path);

		if (Input::get('download'))
		{
			$response = Response::make($file, 200);

			$response->header('Content-Disposition', 'attachment; filename="'.$media->name.'"');
			$response->header('Content-Type', $media->mime);
			$response->header('Content-Length', strlen($file));
			$response->header('Connection', 'close');

			return $response;
		}

		if ( ! $media->is_image)
		{
			$response = Response::make($file, 200);

			$response->header('Content-Type', $media->mime);

			return $response;
		}

		$img = Image::make($file);

		if ($size)
		{
			$matches = explode('x', $size);

			$width = array_get($matches, 0);

			$height = array_get($matches, 1) ?: $width;

			if (Input::get('crop'))
			{
				$img->crop($width, $height);
			}
			else
			{
				$img->resize($width, $height, true);
			}
		}

		$img->cache(function($image) use ($img) {
			return $image->make($img);
		});

		return $img->response();
	}

}
