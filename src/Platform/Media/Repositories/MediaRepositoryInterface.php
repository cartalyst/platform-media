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
	 * Get a media by it's primary key.
	 *
	 * @param  int  $id
	 * @return \Platform\Media\Media
	 */
	public function find($id);

	/**
	 * Get a media by it's file path.
	 *
	 * @param  string  $path
	 * @return \Platform\Media\Media
	 */
	public function findByPath($path);

	/**
	 * Returns all the media files by the given tags.
	 *
	 * @param  mixed  $tags
	 * @return \Platform\Media\Media
	 */
	public function findAllByTag($tags);

	/**
	 * Returns a list of the available tags.
	 *
	 * @return array
	 */
	public function getTags();

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
	 * Determine if the given media is valid for updating.
	 *
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Upload the given file.
	 *
	 * @param  Symfony\Component\HttpFoundation\File\UploadedFile  $file
	 * @return bool
	 */
	public function upload(UploadedFile $file);

	/**
	 * Creates a media with the given data.
	 *
	 * @param  array  $data
	 * @return \Cartalyst\Media\Media
	 */
	public function create($data);

	/**
	 * Updates a media with the given data.
	 *
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Cartalyst\Media\Media
	 */
	public function update($id, array $data);

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
