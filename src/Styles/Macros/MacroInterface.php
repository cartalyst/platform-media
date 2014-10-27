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
use Intervention\Image\ImageManager;
use Platform\Media\Models\Media;

interface MacroInterface {

	public function getMedia();

	public function setMedia(Media $media);

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
	 * @return mixed
	 */
	public function setFile(File $file);

	/**
	 * Executes the filter.
	 *
	 * @return ..
	 */
	public function run();

}
