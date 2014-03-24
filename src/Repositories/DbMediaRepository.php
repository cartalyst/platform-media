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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Media\Exceptions\FileExistsException;
use Cartalyst\Media\Exceptions\InvalidFileException;
use Cartalyst\Media\Exceptions\InvalidMimeTypeException;
use Cartalyst\Media\Exceptions\MaxFileSizeExceededException;
use Event;
use File;
use Lang;
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
	protected $rules = [
		'name' => 'required',
	];

	/**
	 * Holds the occurred error.
	 *
	 * @var string
	 */
	protected $error;

	/**
	 * Constructor.
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
	public function findByPath($path)
	{
		return $this->createModel()->where('path', $path)->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllByTags($tags)
	{
		$query = $this->createModel()->newQuery();

		foreach ((array) $tags as $tag)
		{
			$query->where('tags', 'LIKE', "%{$tag}%");
		}

		return $query->get();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTags()
	{
		$tags = [];

		foreach ($this->createModel()->newQuery()->lists('tags') as $_tags)
		{
			$tags = array_merge($_tags, $tags);
		}

		return array_unique($tags);
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
	public function upload(UploadedFile $file, $tags = [])
	{
		try
		{
			$uploaded = Media::upload($file);

			if ( ! $media = $this->findByPath($uploaded->getPath()))
			{
				$imageSize = $uploaded->getImageSize();

				$media = $this->createModel()->create([
					'name'      => $file->getClientOriginalName(),
					'path'      => $uploaded->getPath(),
					'extension' => $uploaded->getExtension(),
					'mime'      => $uploaded->getMimetype(),
					'size'      => $uploaded->getSize(),
					'is_image'  => $uploaded->isImage(),
					'width'     => $imageSize['width'],
					'height'    => $imageSize['height'],
					'tags'      => $tags,
				]);
			}

			Event::fire('platform.media.uploaded', [$media, $uploaded, $file]);

			return $media->toArray();
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
	public function create($data)
	{
		return with($model = $this->createModel())->create($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data, $file = null)
	{
		$model = $this->find($id);

		if ($file instanceof UploadedFile)
		{
			if ($this->validForUpload($file))
			{
				// Delete the old media file
				Media::delete($model->path);

				File::delete(media_cache_path($model->thumbnail));

				// Upload the new file
				$uploaded = Media::upload($file);

				Event::fire('platform.media.uploaded', [$model, $uploaded, $file]);

				$imageSize = $uploaded->getImageSize();

				// Update the media entry
				$model->fill([
					'path'      => $uploaded->getPath(),
					'extension' => $uploaded->getExtension(),
					'mime'      => $uploaded->getMimetype(),
					'size'      => $uploaded->getSize(),
					'is_image'  => $uploaded->isImage(),
					'width'     => $imageSize['width'],
					'height'    => $imageSize['height'],
				]);
			}
			else
			{
				return false;
			}
		}

		$model->fill($data);

		$model->save();

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

			File::delete(media_cache_path($model->thumbnail));

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
		$validator = Validator::make($data, $this->rules);

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
