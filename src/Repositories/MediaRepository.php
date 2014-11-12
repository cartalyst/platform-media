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
	public function getAllTags()
	{
		return $this->createModel()->allTags()->lists('name');
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

			// Get the submitted tags
			$tags = array_pull($input, 'tags', []);

			// Upload the file
			$file = $this->filesystem->upload($uploadedFile, $fileName);

			// Check if a media entry already exists for this path
			if ( ! $media = $this->findByPath($file->getPath()))
			{
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

				$media = $this->createModel();
			}

			$media->fill($input)->save();

			// Set the tags on the media entry
			$this->tags->set($media, $tags);

			$this->fireEvent('platform.media.uploaded', [ $media, $file, $uploadedFile ]);

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
	public function update($id, array $input, $uploadedFile = null)
	{
		//
		$media = $this->find($id);

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

				$imageSize = $uploaded->getImageSize();

				// Update the media entry
				$input = array_merge([
					'path'      => $uploaded->getPath(),
					'extension' => $uploaded->getExtension(),
					'mime'      => $uploaded->getMimetype(),
					'size'      => $uploaded->getSize(),
					'is_image'  => $uploaded->isImage(),
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
		$media->fill(array_except($input, 'tags'))->save();

		return $media;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($media = $this->find($id))
		{
			$this->filesystem->delete($media->path);

			$file = ''; # read the file

			//$this->fireEvent('platform.media.deleted', [ $media, $file ]);

			$media->delete();

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
