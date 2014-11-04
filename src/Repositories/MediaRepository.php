<?php namespace Platform\Media\Repositories;
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

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use Cartalyst\Filesystem\Exceptions\FileExistsException;
use Cartalyst\Filesystem\Exceptions\InvalidFileException;
use Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException;
use Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException;
use File;

class MediaRepository implements MediaRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The Filesystem instance.
	 *
	 * @var \Cartalyst\Filesystem\Filesystem
	 */
	protected $filesystem;

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
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->setContainer($app);

		$this->setDispatcher($app['events']);

		$this->filesystem = $app['filesystem'];

		$this->setValidator($app['platform.media.validator']);

		$this->setModel(get_class($app['Platform\Media\Models\Media']));
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
		return $this->createModel()->rememberForever('platform.media.'.$id)->find($id);
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
	public function validForUpdate($id, array $data)
	{
		return $this->validator->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpload(UploadedFile $file)
	{
		try
		{
			$this->filesystem->validateFile($file);

			return true;
		}
		catch (InvalidFileException $e)
		{
			$this->setError(trans('platform/media::message.invalid_file'));
		}
		catch (MaxFileSizeExceededException $e)
		{
			$this->setError(trans('platform/media::message.file_size_exceeded'));
		}
		catch (InvalidMimeTypeException $e)
		{
			$this->setError(trans('platform/media::message.invalid_mime'));
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function upload(UploadedFile $uploadedFile, array $input)
	{
		try
		{
			// Sanitize the file name
			$fileName = $this->sanitizeFileName(
				array_get($input, 'name', $uploadedFile->getClientOriginalName())
			);

			//
			$file = $this->filesystem->upload($uploadedFile, $fileName);

			if ( ! $media = $this->findByPath($file->getPath()))
			{
				if ($file->isImage())
				{
					$imageSize = $file->getImageSize();
				}
				else
				{
					$imageSize = [ 'width' => 0, 'height' => 0 ];
				}

				$data = array_merge([
					'name'      => $uploadedFile->getClientOriginalName(),
					'path'      => $file->getPath(),
					'extension' => $file->getExtension(),
					'mime'      => $file->getMimetype(),
					'size'      => $file->getSize(),
					'is_image'  => $file->isImage(),
					'width'     => $imageSize['width'],
					'height'    => $imageSize['height'],
				], array_except($input, 'tags'));

				$media = $this->createModel();
				$media->fill($data)->save();

				//$media->tag(array_get($input, 'tags', []));
			}

			# maybe move this to the event handler ?!
			app('platform.media.manager')->handle($uploadedFile, $file, $media);

			# $this->fireEvent('platform.media.uploaded', [ $uploadedFile, $file, $media ]);

			return $this->find($media->id)->toJson();
		}
		catch (FileExistsException $e)
		{
			$this->setError(trans('platform/media::message.file_exists'));

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
		$tags = array_get($data, 'tags', []);

		$model = $this->find($id);

		if ($file instanceof UploadedFile)
		{
			if ($this->validForUpload($file))
			{
				// Delete the old media file
				$this->filesystem->delete($model->path);

				File::delete(media_cache_path($model->thumbnail));

				// Upload the new file
				$uploaded = $this->filesystem->upload($file);

				$this->fireEvent('platform.media.uploaded', [$model, $uploaded, $file]);

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

		// Get the current media tags
		$mediaTags = $model->tags->lists('name');

		// Prepare the tags to be added and removed
		$tagsToAdd = array_diff($tags, $mediaTags);
		$tagsToDel = array_diff($mediaTags, $tags);

		// Detach the tags
		if ( ! empty($tagsToDel)) $model->untag($tagsToDel);

		// Attach the tags
		if ( ! empty($tagsToAdd)) $model->tag($tagsToAdd);

		// Clear the tags cache
		$this->container['cache']->forget('platform.tags.all');

		// Update the media entry
		$model->fill(array_except($data, 'tags'))->save();

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($model = $this->find($id))
		{
			$this->filesystem->delete($model->path);

			File::delete(media_cache_path($model->thumbnail));

			$model->delete();

			return true;
		}

		$this->setError(trans('platform/media::message.error.delete'));

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

	protected function sanitizeFileName($fileName)
	{
		$regex = [ '#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '#[ ]#', '![_]+!u' ];

		return preg_replace($regex, '_', strtolower($fileName));
	}

}
