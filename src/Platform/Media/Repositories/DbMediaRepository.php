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
use Lang;
use League\Flysystem\FileExistsException;
use Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Validator;

class DbMediaRepository implements MediaRepositoryInterface {

	/**
	 * The Eloquent media model
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'required',
	);

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
	public function find($id)
	{
		return $this->createModel()->find($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		return $this->validateMedia($data, $id);
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

			$media = with($model = $this->createModel())->create(array(
				'name'      => $file->getClientOriginalName(),
				'path'      => $data->getPath(),
				'extension' => $data->getExtension(),
				'mime'      => $data->getMimetype(),
				'size'      => $data->getSize(),
				'is_image'  => $data->isImage(),
				'width'     => $imageSize['width'],
				'height'    => $imageSize['height']
			));

			$media->unique_id = $model->generateUniqueId($media->id);
			$media->save();

			return true;
		}
		catch (FileExistsException $e)
		{
			$this->setError(Lang::get('platform/media::message.file_exists'));

			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$model = $this->find($id);

		$model->fill($data)->save();

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($model = $this->find($id))
		{
			Media::delete($model->path);

			$model->delete();

			return true;
		}

		$this->setError(Lang::get('platform/media::message.error.delete'));

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
	 * Validates a media.
	 *
	 * @param  array  $data
	 * @param  mixed  $id
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validateMedia($data, $id = null)
	{
		$rules = $this->rules;

		$validator = Validator::make($data, $rules);

		$validator->passes();

		return $validator->errors();
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
