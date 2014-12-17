<?php namespace Platform\Media\Handlers;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Media\Models\Media;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface EventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a media is being created.
	 *
	 * @return mixed
	 */
	#public function creating();

	/**
	 * When a media is created.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return mixed
	 */
	#public function created(Media $media);

	/**
	 * When a media is being updated.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return mixed
	 */
	#public function updating(Media $media);

	/**
	 * When a media is updated.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return mixed
	 */
	#public function updated(Media $media);

	/**
	 * When a media is deleted.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @return mixed
	 */
	#public function deleted(Media $media);

}
