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

	public function onDelete($file)
	{
		if ($media = $this->media->findByPath($file->getPath()))
		{
			File::delete(media_cache_path($media->thumbnail));

			$media->delete();
		}
	}

	public function onUpload($media, $file, $original)
	{
		$imageSize = $file->getImageSize();

		$path = null;

		if ($file->isImage())
		{
			$width = 245;
			$height = 200;

			$extension = $file->getExtension();

			$filename = str_replace(".{$extension}", '', $original->getClientOriginalName());

			$name = implode(array($filename, $width, $height ?: $width), ' ');

			$path = Str::slug($name).'.'.$extension;

			$data = Media::getFileSystem()->read($file->getPath());

			$img = Image::make($data)
				->resize(null, $height, true, false)
				->save(media_cache_path($path));

			$media->thumbnail = $path;
			$media->save();
		}
	}

	public function subscribe($events)
	{
		$events->listen('cartalyst.media.deleted', 'Platform\Media\Handlers\MediaEventHandler@onDelete');
		$events->listen('platform.media.uploaded', 'Platform\Media\Handlers\MediaEventHandler@onUpload');
	}

}
