<?php namespace Platform\Media;
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

use \Illuminate\Filesystem\Filesystem;

class Media extends \Illuminate\Database\Eloquent\Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	public $table = 'media';

	/**
	 * Holds the Illuminate filesystem.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * Delete the media from the database, and
	 * deletes the associated file in the filesystem.
	 *
	 * @return void
	 */
	public function delete()
	{
		if ($this->filesystem->exists($this->file_path))
		{
			$this->filesystem->delete($this->file_path);
		}

		parent::delete();
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
