<?php
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

/**
 *
 *
 * @param  string  $
 * @param  string  $options
 * @return string
 */
function media_chooser()
{
	# todo...
}

/**
 * Outputs the media url of the given
 * media.
 *
 * @param  mixed  $media
 * @return mixed
 */
function get_media($media = null)
{
	// Check if this media is already a valid URL
	if(filter_var($media, FILTER_VALIDATE_URL) !== false)
	{
		return $media;
	}

	try
	{
		// Get the media information
		$request = API::get('media/' . $media);
		$media   = $request['media'];

		// Return the media url
		return $media->url;
	}
	catch(Cartalyst\Api\ApiHttpException $e)
	{
		return false;
	}
}

/**
 * Returns the full path to the media storage
 * directory.
 *
 * @return string
 */
function media_storage_directory()
{
	return app('path.base') . '/public/' . Config::get('platform/media::media.directory', 'platform/media/');
}

