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

	public function onUpload($file, $original)
	{
		$imageSize = $file->getImageSize();

		$path = null;

		if ($file->isImage())
		{
			# make these values configurable?!
			$width = 176;
			$height = 176;

			$extension = $file->getExtension();

			$filename = str_replace(".{$extension}", '', $original->getClientOriginalName());

			$name = implode(array($filename, $width, $height ?: $width), ' ');

			#$path = media_cache_path() . Str::slug($name) . '.' . $extension;
			$path = Str::slug($name) . '.' . $extension;

			if ( ! File::exists(media_cache_path($path)))
			{
				$data = Media::getFileSystem()->read($file->getPath());

				Image::make($data)->crop($width, $height, true)->save(media_cache_path($path));
			}
		}

		$media = $this->media->create(array(
			'name'      => $original->getClientOriginalName(),
			'path'      => $file->getPath(),
			'extension' => $file->getExtension(),
			'mime'      => $file->getMimetype(),
			'size'      => $file->getSize(),
			'is_image'  => $file->isImage(),
			'width'     => $imageSize['width'],
			'height'    => $imageSize['height'],
			'thumbnail' => $path,
		));
	}

	public function subscribe($events)
	{
		$events->listen('cartalyst.media.uploaded', 'Platform\Media\Handlers\MediaEventHandler@onUpload');
	}

}
