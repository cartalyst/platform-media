<?php namespace Platform\Media\Styles;
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

use Closure;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Manager {

	/**
	 * The registered styles.
	 *
	 * @var array
	 */
	protected $styles = [];

	/**
	 * The registered macros.
	 *
	 * @var array
	 */
	protected $macros = [];

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 * @return void
	 */
	public function __construct(Container $app)
	{
		// Get the config
		$config = $app['config']->get('platform/media::config');

		// Register the styles from the config
		call_user_func(array_get($config, 'styles'), $this);

		// Register the macros from the config
		call_user_func(array_get($config, 'macros'), $this);
	}

	/**
	 * Returns all the registered styles.
	 *
	 * @return array
	 */
	public function getStyles()
	{
		return $this->styles;
	}

	/**
	 * Sets a new style.
	 *
	 * @param  string  $name
	 * @param  \Closure  $callable
	 * @return $this
	 */
	public function setStyle($name, Closure $callable)
	{
		$this->styles[$name] = $callable;

		return $this;
	}

	/**
	 * Returns all the registered macros.
	 *
	 * @return array
	 */
	public function getMacros()
	{
		return $this->macros;
	}

	/**
	 * Sets a new macro.
	 *
	 * @param  string  $name
	 * @param  \Closure|string  $callable
	 * @return $this
	 */
	public function setMacro($name, $callable)
	{
		$this->macros[$name] = $callable;

		return $this;
	}

	/**
	 *
	 *
	 * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
	 * @param  \Cartalyst\Filesystem\File  $storedFile
	 * @param  \Platform\Media\Models\Media  $media
	 * @return void
	 */
	public function handle(UploadedFile $uploadedFile, File $storedFile, Media $media)
	{
		// Get the uploaded file mime type
		$mimeType = $uploadedFile->getMimeType();

		// Loop through all the registered styles
		foreach ($this->getStyles() as $name => $style)
		{
			// Initialize the style
			call_user_func($style, $style = new Style($name));

			// Check if the uploaded file mime type is valid
			if ($style->mimes && ! in_array($mimeType, $style->mimes)) continue;

			// Loop through the style macros
			foreach ($style->macros as $name => $macro)
			{
				// Initialize the macro
				$macro = $this->initializeMacro($macro);

				// Set the requirements on the macro
				$macro->setMedia($media);
				$macro->setStyle($style);
				$macro->setFile($storedFile);
				$macro->setUploadedFile($uploadedFile);

				// Execute the macro
				$macro->run();
			}
		}
	}

	protected function initializeMacro($macro)
	{
		// Get the macro class name or class object
		$macro = array_get($this->getMacros(), $macro);

		if (is_string($macro))
		{
			$macro = app($macro);
		}
		elseif ($macro instanceof Closure)
		{
			$macro = $macro();
		}

		return $macro;
	}

}
