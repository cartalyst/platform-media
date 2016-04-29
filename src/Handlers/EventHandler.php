<?php

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
 * @version    3.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Handlers;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Events\Dispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen('platform.media.uploaded', __CLASS__.'@uploaded');

        $dispatcher->listen('platform.media.updating', __CLASS__.'@updating');
        $dispatcher->listen('platform.media.updated', __CLASS__.'@updated');

        $dispatcher->listen('platform.media.deleting', __CLASS__.'@deleting');
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
        if ($thumbnail = $media->thumbnail)
        {
            \Illuminate\Support\Facades\File::delete($thumbnail);
        }

        $this->app['platform.media.manager']->handleUp($media, $file, $uploadedFile);

        $this->flushCache($media);
    }

    /**
     * On updating event.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function updating(Media $media)
    {
    }

    /**
     * On updated event.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function updated(Media $media)
    {
        $this->flushCache($media);
    }

    /**
     * On deleting event.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function deleting(Media $media, File $file)
    {
        $this->app['platform.media.manager']->handleDown($media, $file);
    }

    /**
     * On deleted event.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function deleted(Media $media)
    {
        $this->flushCache($media);
    }

    /**
     * Flush the cache.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    protected function flushCache(Media $media)
    {
        $this->app['cache']->forget('platform.media.'.$media->id);
        $this->app['cache']->forget('platform.media.path.'.$media->path);
    }
}
