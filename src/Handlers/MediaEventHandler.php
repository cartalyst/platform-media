<?php namespace Platform\Media\Handlers;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use File;
use Filesystem;
use Image;
use Platform\Media\Repositories\MediaRepositoryInterface;
use Str;
use URL;

class MediaEventHandler {

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
		$this->media = $media;
	}

	public function onUpload($media, $file, $original)
	{
		$path = null;

		if ($file->isImage())
		{
			$width = 40;
			$height = 40;

			$extension = $file->getExtension();

			$imageSize = $file->getImageSize();

			$filename = str_replace(".{$extension}", '', $original->getClientOriginalName());

			$name = Str::slug(implode([$filename, $width, $height ?: $width], ' '));

			$path = "{$media->id}_{$name}.{$extension}";

			$data = Filesystem::read($file->getPath());

			$img = Image::make($data)
				->resize($width, $height)
				->save(media_cache_path($path));

			$media->thumbnail = $path;
			$media->save();
		}
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $events
	 * @return void
	 */
	public function subscribe($events)
	{
		$events->listen('platform.media.uploaded', 'Platform\Media\Handlers\MediaEventHandler@onUpload');
	}

}
