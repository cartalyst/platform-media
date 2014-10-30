<?php namespace Platform\Media\Styles\Macros;
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

use Illuminate\Container\Container;

class ResizeMacro extends AbstractMacro implements MacroInterface {

	/**
	 * The Illuminate Container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The Filesystem instance.
	 *
	 * @var \Cartalyst\Filesystem\Filesystem
	 */
	protected $filesystem;

	/**
	 * The Intervention Image Manager instance.
	 *
	 * @var \Intervention\Image\ImageManager
	 */
	protected $intervention;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;

		$this->intervention = $app['image'];

		$this->filesystem = $app['filesystem'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		$file = $this->getFile();

		$media = $this->getMedia();

		$uploadedFile = $this->getUploadedFile();

		if ($file->isImage())
		{
			// Get the style
			$width = $this->style->width;
			$height = $this->style->height;

			// Get the file extension
			$extension = $file->getExtension();

			// Get the file name without the file extension
			$filename = str_replace(".{$extension}", '', $uploadedFile->getClientOriginalName());

			//
			$name = \Str::slug(implode([ $filename, $width, $height ?: $width ], ' '));

			// Prepare the thumbnail path
			$path = str_replace(public_path(), null, "{$this->style->storage_path}/{$media->id}_{$name}.{$extension}");

			// Update the media entry
			$media->thumbnail = $path;
			$media->save();

			// Create the thumbnail
			$this->intervention
				->make($this->filesystem->read($file->getPath()))
				->resize($width, $height)
				->save(public_path($path));
		}
	}

}
