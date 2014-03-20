<?php namespace Platform\Media\Widgets;

use Platform\Media\Repositories\MediaRepositoryInterface;
use URL;

class Media {

	protected $data = array();

	public function __construct(MediaRepositoryInterface $media)
	{
		$this->media = $media;
	}

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
