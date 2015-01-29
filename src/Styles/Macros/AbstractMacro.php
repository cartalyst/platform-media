<?php namespace Platform\Media\Styles\Macros;
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
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Filesystem\File;
use Platform\Media\Styles\Style;
use Platform\Media\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractMacro implements MacroInterface {

	/**
	 * The Style object.
	 *
	 * @var \Platform\Media\Styles\Style
	 */
	protected $style;

	/**
	 * {@inheritDoc}
	 */
	public function getStyle()
	{
		return $this->style;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setStyle(Style $style)
	{
		$this->style = $style;

		return $this;
	}

}
