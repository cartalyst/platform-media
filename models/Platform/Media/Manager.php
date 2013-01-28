<?php namespace Platform\Media;


class Manager
{

	protected $app;

	public function __construct()
	{
		$this->app = app();
	}


	/**
	 * Returns the storage media directory.
	 *
	 * @return string
	 */
	public function directory()
	{
		return $this->app['path.base'] . '/public/' . \Config::get('platform/media::media.directory');
	}

	/**
	 * Returns the temporary storage media directory.
	 *
	 * @return string
	 */
	public function temporaryDirectory()
	{

	}

}
