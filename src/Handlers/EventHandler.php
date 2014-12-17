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

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.media.uploaded', __CLASS__.'@uploaded');

		$dispatcher->listen('platform.media.deleted', __CLASS__.'@deleted');
	}

	/**
	 * On upload event.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @param  \Cartalyst\Filesystem\File  $file
	 * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
	 * @return void
	 */
	public function uploaded(Media $media, File $file, UploadedFile $uploadedFile)
	{
		\Illuminate\Support\Facades\File::delete($media->thumbnail);

		app('platform.media.manager')->handleUp($media, $file, $uploadedFile);
	}

	/**
	 * On deleted event.
	 *
	 * @param  \Platform\Media\Models\Media  $media
	 * @param  \Cartalyst\Filesystem\File  $file
	 * @return void
	 */
	public function deleted(Media $media, File $file)
	{
		\Illuminate\Support\Facades\File::delete($media->thumbnail);

		app('platform.media.manager')->handleDown($media, $file);
	}

}
