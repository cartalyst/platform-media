<?php namespace Platform\Media\Widgets;
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
