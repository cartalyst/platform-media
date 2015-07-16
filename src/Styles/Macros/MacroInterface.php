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
 * @version    1.1.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Filesystem\File;
use Platform\Media\Styles\Style;
use Platform\Media\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MacroInterface {

	/**
	 * Returns the Style.
	 *
	 * @return \Platform\Media\Styles\Style
	 */
	public function getStyle();

	/**
	 * Sets the Style.
	 *
	 * @param  \Platform\Media\Styles\Style  $style
	 * @return $this
	 */
	public function setStyle(Style $style);

	/**
	 * Executes the macro.
	 *
	 * @return void
	 */
	public function up(Media $media, File $file, UploadedFile $uploadedFile);

	/**
	 * Reverts the executed macro.
	 *
	 * @return void
	 */
	public function down(Media $media, File $file);

}
