<?php namespace Platform\Media\Repositories;
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

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaRepositoryInterface {

	/**
	 * Return a dataset compatible with the data grid.
	 *
	 * @return mixed
	 */
	public function grid();

	/**
	 * Determine if the given file is valid for upload.
	 *
	 * @param  Symfony\Component\HttpFoundation\File\UploadedFile  $file
	 * @return bool
	 * @throws Cartalyst\Media\Exceptions\InvalidFileException
	 * @throws Cartalyst\Media\Exceptions\MaxFileSizeExceededException
	 * @throws Cartalyst\Media\Exceptions\InvalidMimeTypeException
	 */
	public function validForUpload(UploadedFile $file);

	/**
	 * Upload the given file.
	 *
	 * @param  Symfony\Component\HttpFoundation\File\UploadedFile  $file
	 * @return bool
	 */
	public function upload(UploadedFile $file);

	/**
	 * Deletes the given media.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

	/**
	 * Return the occurred error.
	 *
	 * @return string
	 */
	public function getError();

	/**
	 * Set the occurred error.
	 *
	 * @param  string  $error
	 * @return void
	 */
	public function setError($error);

}
