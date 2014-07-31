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

interface FilterInterface {

	public function getConfig();

	public function setConfig(array $config);

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
	 * Returns the Intervention Image instance.
	 *
	 * @return \Intervention\Image\ImageManager
	 */
	public function getIntervention();

	/**
	 * Sets the Intervention Image instance.
	 *
	 * @param  \Intervention\Image\ImageManager  $intervention
	 * @return mixed
	 */
	public function setIntervention(ImageManager $intervention);

	/**
	 * Executes the filter.
	 *
	 * @return ..
	 */
	public function run();

}
