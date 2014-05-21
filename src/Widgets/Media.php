<?php namespace Platform\Media\Widgets;
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Media\Repositories\MediaRepositoryInterface;
use URL;

class Media {

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
	 * @return void
	 */
	public function __construct(MediaRepositoryInterface $media)
	{
		$this->media = $media;
	}

	/**
	 * Returns the given media path.
	 *
	 * @param  int  $id
	 * @param  string  $type
	 * @return string
	 */
	public function show($id, $type = null)
	{
		if ($media = $this->media->find((int) $id))
		{
			switch ($type)
			{
				case 'thumbnail':

					$url = media_cache_path($media->thumbnail);

					break;

				case 'download':

					$url = "media/download/{$media->path}";

					break;

				default:

					$url = "media/{$media->path}";

					break;
			}

			return URL::to($url);
		}
	}

}
