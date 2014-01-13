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

use Cartalyst\Media\Exceptions\InvalidFileException;
use Cartalyst\Media\Exceptions\InvalidMimeTypeException;
use Cartalyst\Media\Exceptions\MaxFileSizeExceededException;
use Config;
use Flysystem\FileExistsException;
use Lang;
use Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DbMediaRepository implements MediaRepositoryInterface {

	/**
	 * The Eloquent media model
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Holds the occurred error.
	 *
	 * @var string
	 */
	protected $error;

	/**
	 * Start it up.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this->createModel();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpload(UploadedFile $file)
	{
		try
		{
			Media::validateFile($file);

			return true;
		}
		catch (InvalidFileException $e)
		{
			$this->setError(Lang::get('platform/media::message.invalid_file'));
		}
		catch (MaxFileSizeExceededException $e)
		{
			$this->setError(Lang::get('platform/media::message.file_size_exceeded'));
		}
		catch (InvalidMimeTypeException $e)
		{
			$this->setError(Lang::get('platform/media::message.invalid_mime'));
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function upload(UploadedFile $file)
	{
		try
		{
			$data = Media::upload($file);

			$imageSize = $data->getImageSize();

			with($model = $this->createModel())->create(array(
				'name'      => $file->getClientOriginalName(),
				'path'      => $data->getPath(),
				'extension' => $data->getExtension(),
				'mime'      => $data->getMimetype(),
				'size'      => $data->getSize(),
				'width'     => $imageSize['width'],
				'height'    => $imageSize['height']
			));

			return true;
		}
		catch (FileExistsException $e)
		{
			$this->setError(Lang::get('platform/media::messages.file_exists'));

			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($model = $this->find($id))
		{
			Media::delete($model->file_path);

			$model->delete();

			return true;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setError($error)
	{
		$this->error = $error;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
