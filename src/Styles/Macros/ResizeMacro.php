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
	 * The Intervention Image Manager instance.
	 *
	 * @var \Intervention\Image\ImageManager
	 */
	protected $intervention;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void*/
	public function __construct(Container $app)
	{
		$this->app = $app;

		$this->intervention = $app['image'];
	}

	/**
	 * @{inheritDoc}
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

			$extension = $file->getExtension();

			$filename = str_replace(".{$extension}", '', $uploadedFile->getClientOriginalName());

			$name = \Str::slug(implode([ $filename, $width, $height ?: $width ], ' '));

			//
			$path = "{$this->style->storage_path}/{$media->id}_{$name}.{$extension}";

			// Update the media entry
			$media->thumbnail = $path;
			$media->save();

			// Create the thumbnail
			$this->intervention
				->make(\Filesystem::read($file->getPath()))
				->resize($width, $height)
				->save($path);
		}
	}

}
