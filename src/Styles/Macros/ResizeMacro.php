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

class ResizeMacro extends AbstractMacro {

	protected $container;

	protected $intervention;

	public function __construct(Container $app)
	{
		$this->container = $app;

		$this->intervention = $app['image'];
	}

	/**
	 * @{inheritDoc}
	 */
	public function run()
	{
		$style = $this->style;

		$file = $this->getFile();

		$media = $this->getMedia();

		$uploadedFile = $this->getUploadedFile();

		if ($file->isImage())
		{
			$width = $style->width;
			$height = $style->height;

			$extension = $file->getExtension();

			$imageSize = $file->getImageSize();

			$filename = str_replace(".{$extension}", '', $uploadedFile->getClientOriginalName());

			$name = \Str::slug(implode([$filename, $width, $height ?: $width], ' '));

			$path = "{$media->id}_{$name}.{$extension}";

			$data = \Filesystem::read($file->getPath());

			$media_public_path = public_path(media_cache_path($path));

			$img = $this->intervention->make($data)->resize($width, $height)->save($media_public_path);

			$media->thumbnail = $path;
			$media->save();
		}
	}

}
