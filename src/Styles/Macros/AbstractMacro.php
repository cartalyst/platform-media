<?php namespace Platform\Media\Styles\Macros;
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
use Platform\Media\Styles\Style;
use Platform\Media\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractMacro implements MacroInterface {

	/**
	 * The file that was stored on the filesystem.
	 *
	 * @var \Cartalyst\Filesystem\File
	 */
	protected $file;

	/**
	 * The Media model instance the file belongs to.
	 *
	 * @var \Platform\Media\Models\Media
	 */
	protected $media;

	/**
	 * The Style object.
	 *
	 * @var \Platform\Media\Styles\Style
	 */
	protected $style;

	/**
	 * The uploaded file.
	 *
	 * @var \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	protected $uploadedFile;

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
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUploadedFile()
	{
		return $this->uploadedFile;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUploadedFile(UploadedFile $uploadedFile)
	{
		$this->uploadedFile = $uploadedFile;

		return $this;
	}

}
