<?php namespace Platform\Media\Widgets;

use Platform\Media\Repositories\MediaRepositoryInterface;
use URL;

use File;

class Media {

	protected $data = array();

	public function __construct(MediaRepositoryInterface $media)
	{
		$this->media = $media;
	}

	public function show($entity, $width = null, $height = null)
	{
		/*
		if (empty($this->data))
		{
			$this->data = $this->media->all();
		}

		$data = $this->data[$entity];
		*/

		if ( ! $media = $this->media->find($entity))
		{
			return $entity;
		}


		$filename = str_replace(".{$media->extension}", '', $media->path);

		$name = implode(array($filename, $width, $height ?: $width), ' ');

		$path = 'cache/media/' . \Str::slug($name) . '.' . $media->extension;

		if ( ! File::exists($path))
		{
			$file = \Media::getFileSystem()->read($media->path);

			$img = \Image::make($file)->resize($width, $height, true)->save($path);
		}

		return URL::to($path);
	}


	public function upload()
	{
		/*

			// Allow only and all the images mime types to be uploaded
			@widget(..., array('#myMediaForm', 'image/*'))

			// Allow only txt and png images
			@widget(..., array('#myMediaForm', 'image/png, text/plain'))

			// Associate uploaded images to an extension
			@widget(..., array('#myMediaForm', null, 'platform/users'))


			## will need an extra parameter, probably, so that we can associate
			## media to certain user groups, user ids, etc...
			## once the media get's uploaded we call a certain class method
			## would work similarly to the menu types....

		*/
	}

}
