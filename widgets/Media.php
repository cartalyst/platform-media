<?php namespace Platform\Media\Widgets;

use Platform\Media\Repositories\MediaRepositoryInterface;
use URL;

class Media {

	protected $data = array();

	public function __construct(MediaRepositoryInterface $media)
	{
		$this->media = $media;
	}

	public function show($id)
	{
		if ($media = $this->media->find((int) $id))
		{
			return URL::to("media/{$media->path}");
		}
	}

}
