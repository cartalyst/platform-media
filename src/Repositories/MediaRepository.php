<?php namespace Platform\Media\Repositories;
/**
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
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Filesystem\Exceptions\FileExistsException;
use Cartalyst\Filesystem\Exceptions\InvalidFileException;
use Cartalyst\Filesystem\Exceptions\InvalidMimeTypeException;
use Cartalyst\Filesystem\Exceptions\MaxFileSizeExceededException;

class MediaRepository implements MediaRepositoryInterface {

	use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The Filesystem instance.
	 *
	 * @var \Cartalyst\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * The Eloquent model name.
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
	 * The Tags repository instance.
	 *
	 * @var \Platform\Tags\Repositories\TagsRepositoryInterface
	 */
	protected $tags;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->setContainer($app);

		$this->tags = $app['platform.tags'];

		$this->setDispatcher($app['events']);

		$this->filesystem = $app['cartalyst.filesystem'];

		$this->setValidator($app['platform.media.validator']);

		$this->setModel(get_class($app['Platform\Media\Models\Media']));
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		return $this->createModel()->with('tags');
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->container['cache']->rememberForever('platform.media.'.$id, function() use ($id)
		{
			return $this->createModel()->find($id);
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByPath($path)
	{
		return $this->container['cache']->rememberForever('platform.media.path.'.$path, function() use ($path)
		{
			return $this->createModel()->wherePath($path)->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllTags()
	{
		return $this->createModel()->allTags()->lists('name');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllowedMimes()
	{
		return $this->filesystem->getAllowedMimes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		return $this->validator->on('update')->validate($data);
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
			$media = $this->createModel();

			$media->save();

			// Sanitize the file name
			$fileName = $this->prepareFileName(
				array_get($input, 'name', $uploadedFile->getClientOriginalName()),
				$media->id
			);

			// Get the submitted tags
			$tags = array_pull($input, 'tags', []);

			// Upload the file
			$file = $this->filesystem->upload($uploadedFile, $fileName);

			// If the file is an image, we get the image size
			$imageSize = $file->getImageSize();

			$input = array_merge([
				'name'      => $uploadedFile->getClientOriginalName(),
				'path'      => $file->getPath(),
				'extension' => $file->getExtension(),
				'mime'      => $file->getMimetype(),
				'size'      => $file->getSize(),
				'is_image'  => $file->isImage(),
				'width'     => $imageSize['width'],
				'height'    => $imageSize['height'],
			], $input);

			$media->fill($input)->save();

			// Set the tags on the media entry
			$this->tags->set($media, $tags);

			$this->fireEvent('platform.media.uploaded', [ $media, $file, $uploadedFile ]);

			return $media;
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
	public function update($id, array $input, $uploadedFile = null)
	{
		$media = $this->find($id);

		$this->fireEvent('platform.media.updating', [ $media ]);

		// Get the submitted tags
		$tags = array_pull($input, 'tags', []);

		if ($uploadedFile instanceof UploadedFile)
		{
			if ($this->validForUpload($uploadedFile))
			{
				// Delete the old media file
				$this->filesystem->delete($media->path);

				// Sanitize the file name
				$fileName = $this->sanitizeFileName(
					array_get($input, 'name', $uploadedFile->getClientOriginalName())
				);

				// Upload the file
				$file = $this->filesystem->upload($uploadedFile, $fileName);

				$this->fireEvent('platform.media.uploaded', [ $media, $file, $uploadedFile ]);

				$imageSize = $file->getImageSize();

				// Update the media entry
				$input = array_merge([
					'path'      => $file->getPath(),
					'extension' => $file->getExtension(),
					'mime'      => $file->getMimetype(),
					'size'      => $file->getSize(),
					'is_image'  => $file->isImage(),
					'width'     => $imageSize['width'],
					'height'    => $imageSize['height'],
				], $input);
			}
			else
			{
				return false;
			}
		}

		// Set the tags on the media entry
		$this->tags->set($media, $tags);

		// Update the media entry
		$media->fill($input)->save();

		$this->fireEvent('platform.media.updated', [ $media ]);

		return $media;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($media = $this->find($id))
		{
			$file = $this->filesystem->get($media->path);

			$this->fireEvent('platform.media.deleting', [ $media, $file ]);

			$this->filesystem->delete($media->path);

			$this->fireEvent('platform.media.deleted', [ $media ]);

			$media->delete();

			return true;
		}

		$this->setError(trans('platform/media::message.error.delete'));

		return false;
	}

	/**
	 * Sets the media private.
	 *
	 * @param  int  $id
	 * @return void
	 */
	public function makePrivate($id)
	{
		if ($media = $this->find($id))
		{
			$media->private = true;
			$media->save();
		}
	}

	/**
	 * Sets the media public.
	 *
	 * @param  int  $id
	 * @return void
	 */
	public function makePublic($id)
	{
		if ($media = $this->find($id))
		{
			$media->private = false;
			$media->save();
		}
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
	 * Sanitizes the file name.
	 *
	 * @param  string  $fileName
	 * @return string
	 */
	protected function sanitizeFileName($fileName)
	{
		$regex = [ '#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#', '#[ ]#', '![_]+!u' ];

		return preg_replace($regex, '_', strtolower($fileName));
	}

	/**
	 * Prepares the filename by sanitizing it and
	 * appending the media id to the end.
	 *
	 * @param  string  $fileName
	 * @param  string  $id
	 * @return string
	 */
	protected function prepareFileName($fileName, $id)
	{
		$fileName = $this->sanitizeFileName($fileName);

		return pathinfo($fileName, PATHINFO_FILENAME)."_{$id}.".pathinfo($fileName, PATHINFO_EXTENSION);
	}

}
