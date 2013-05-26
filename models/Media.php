<?php namespace Platform\Media\Models;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;

class Media extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	public $table = 'media';

	/**
	 * Holds the Illuminate filesystem.
	 *
	 * @var Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * Delete the media from the database, and
	 * deletes the associated file in the filesystem.
	 *
	 * @return void
	 * @todo   Use: $this->file_path once better implemented!
	 */
	public function delete()
	{
		$file = media_storage_directory() . $this->file_name;

		if ($this->filesystem->exists($file))
		{
			$this->filesystem->delete($file);
		}

		parent::delete();
	}

	/**
	 * Returns the converted file size from bytes.
	 *
	 * @param  int  $precision
	 * @return string
	 */
	function file_size($precision = 2)
	{
		$size = $this->file_size;

		$base = log($size) / log(1024);
		$suffixes = array('', 'KB', 'MB', 'GB', 'TB');

		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}

	/**
	 * Returns the file width, this is just stored
	 * if the uploaded file is an image.
	 *
	 * @return string
	 */
	public function width()
	{
		return ($this->width ? $this->width . 'px' : 'n/a');
	}

	/**
	 * Returns the file height, this is just stored
	 * if the uploaded file is an image.
	 *
	 * @return string
	 */
	public function height()
	{
		return ($this->height ? $this->height . 'px' : 'n/a');
	}

	/**
	 * Set the filesystem
	 *
	 * @param Illuminate\Filesystem\Filesystem
	 * @param void
	 */
	public function setFilesystem(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newInstance($attributes = array(), $exists = false)
	{
		// This method just provides a convenient way for us to
		// generate fresh model instances of this current model.
		// It is particularly useful during the hydration of new
		// objects via the Eloquent query builder instances.
		$model = new static((array) $attributes);

		$model->exists = $exists;

		if (isset($this->filesystem))
		{
			$model->setFilesystem($this->filesystem);
		}

		return $model;
	}

}
