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

interface MacroInterface {

	/**
	 * Returns the file.
	 *
	 * @return \Cartalyst\Filesystem\File
	 */
	public function getFile();

	/**
	 * Sets the file.
	 *
	 * @param  \Cartalyst\Filesystem\File  $file
	 * @return $this
	 */
	public function setFile(File $file);

	/**
	 * Returns the Media.
	 *
	 * @return \Platform\Media\Models\Media
	 */
	public function getMedia();

	/**
	 * Sets the Media.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return $this
	 */
	public function setMedia(Media $media);

	/**
	 * Returns the Style.
	 *
	 * @return \Platform\Media\Styles\Style
	 */
	public function getStyle();

	/**
	 * Sets the Style.
	 *
	 * @param  \Platform\Media\Styles\Style  $style
	 * @return $this
	 */
	public function setStyle(Style $style);

	/**
	 * Returns the uploaded file.
	 *
	 * @return \Symfony\Component\HttpFoundation\File\UploadedFile
	 */
	public function getUploadedFile();

	/**
	 * Sets the uploaded file.
	 *
	 * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
	 * @return $this
	 */
	public function setUploadedFile(UploadedFile $uploadedFile);

	/**
	 * Executes the macro.
	 *
	 * @return void
	 */
	public function up();

	/**
	 * Executes the macro.
	 *
	 * @return void
	 */
	public function down();

}
