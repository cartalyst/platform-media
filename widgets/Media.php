<?php namespace Platform\Media\Widgets;

use Platform\Media\Repositories\MediaRepositoryInterface;
use URL;

class Media {

	protected $data = array();

	public function __construct(MediaRepositoryInterface $media)
	{
		$this->media = $media;
	}

	public function show($id, $thumbnail = false)
	{
		if ($media = $this->media->find((int) $id))
		{
			if ($thumbnail)
			{
				$result = media_cache_path($media->thumbnail);
			}
			else
			{
				$result = "media/{$media->path}";
			}

			return URL::to($result);
		}
	}

}
