<?php namespace Platform\Media\Filters;
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

use Cartalyst\Filesystem\File;
use Intervention\Image\ImageManager;
use Platform\Media\Models\Media;

abstract class Abstractfilter implements FilterInterface {

	/**
	 * The style configuration.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The Media model instance the file belongs to.
	 *
	 * @var \Platform\Media\Models\Media
	 */
	protected $media;

	/**
	 *
	 *
	 * @var \Cartalyst\Filesystem\File
	 */
	protected $file;

	/**
	 * The Intervention Image instance.
	 *
	 * @var \Intervention\Image\ImageManager
	 */
	protected $intervention;

	/**
	 * Constructor.
	 *
	 * @param  \Intervention\Image\ImageManager  $intervention
	 * @return void
	 */
	public function __construct(array $config, ImageManager $intervention)
	{
		$this->config = $config;

		$this->intervention = $intervention;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMedia()
	{
		return $this->media;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMedia(Media $media)
	{
		$this->media = $media;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setFile(File $file)
	{
		$this->file = $file;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIntervention()
	{
		return $this->intervention;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setIntervention(ImageManager $intervention)
	{
		$this->intervention = $intervention;

		return $this;
	}

}
