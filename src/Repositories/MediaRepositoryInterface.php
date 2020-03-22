<?php

/*
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    9.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Repositories;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaRepositoryInterface
{
    /**
     * Returns a dataset compatible with data grid.
     *
     * @return mixed
     */
    public function grid();

    /**
     * Returns a media by its primary key.
     *
     * @param int $id
     *
     * @return \Platform\Media\Models\Media
     */
    public function find($id);

    /**
     * Returns a media by its file path.
     *
     * @param string $path
     *
     * @return \Platform\Media\Models\Media
     */
    public function findByPath($path);

    /**
     * Determine if the given file is valid for upload.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @throws \Cartalyst\Filesystem\Exceptions\InvalidFileException
     * @throws \Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException
     * @throws \Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException
     *
     * @return bool
     */
    public function validForUpload(UploadedFile $file);

    /**
     * Determine if the given media is valid for updating.
     *
     * @param array $data
     * @param mixed $id
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function validForUpdate($id, array $data);

    /**
     * Upload the given file.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param array                                               $input
     *
     * @return bool
     */
    public function upload(UploadedFile $file, array $input);

    /**
     * Creates a media with the given data.
     *
     * @param array $data
     *
     * @return \Platform\Media\Models\Media
     */
    public function create($data);

    /**
     * Updates a media with the given data.
     *
     * @param int   $id
     * @param array $data
     *
     * @return \Platform\Media\Models\Media
     */
    public function update($id, array $data);

    /**
     * Deletes the given media.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete($id);

    /**
     * Returns the occurred error.
     *
     * @return string
     */
    public function getError();

    /**
     * Set the occurred error.
     *
     * @param string $error
     *
     * @return void
     */
    public function setError($error);
}
