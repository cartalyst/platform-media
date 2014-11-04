<?php namespace Platform\Media\Handlers;
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
	}

	/**
	 * On upload event.
	 *
	 * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $uploadedFile
	 * @param  \Cartalyst\Filesystem\File  $file
	 * @param  \Platform\Media\Models\Media  $media
	 * @return void
	 */
	public function uploaded(UploadedFile $uploadedFile, File $file, Media $media)
	{
		app('platform.media.manager')->handleUp($uploadedFile, $file, $media);
	}

}
