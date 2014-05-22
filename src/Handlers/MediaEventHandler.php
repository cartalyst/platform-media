<?php namespace Platform\Media\Handlers;

use Platform\Media\Repositories\MediaRepositoryInterface;
use File;
use Image;
use Media;
use Str;
use URL;

class MediaEventHandler {

	/**
	 * Groups repository.
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
		$imageSize = $file->getImageSize();

		$path = null;

		if ($file->isImage())
		{
			$width = 40;
			$height = 40;

			$extension = $file->getExtension();

			$filename = str_replace(".{$extension}", '', $original->getClientOriginalName());

			$name = implode(array($filename, $width, $height ?: $width), ' ');

			$path = Str::slug($name).'.'.$extension;

			$data = Media::read($file->getPath());

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
