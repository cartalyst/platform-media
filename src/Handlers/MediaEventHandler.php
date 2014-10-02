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

use Illuminate\Support\Str;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Events\Dispatcher;
use Intervention\Image\Facades\Image;
use Cartalyst\Support\Handlers\EventHandler;
use Cartalyst\Filesystem\Laravel\Facades\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaEventHandler extends EventHandler {

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.media.uploaded', __CLASS__.'@onUpload');
	}

	/**
	 * On upload event.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @param  \Cartalyst\Filesystem\File  $file
	 * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $original
	 * @return void
	 */
	public function onUpload(Media $media, File $file, UploadedFile $original)
	{
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

			$media_public_path = public_path(media_cache_path($path));

			$img = Image::make($data)
				->resize($width, $height)
				->save($media_public_path);

			$media->thumbnail = $path;
			$media->save();
		}
	}

}
