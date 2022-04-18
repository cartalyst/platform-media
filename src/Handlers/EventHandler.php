<?php

/*
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
 * @version    11.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Handlers;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface
{
    /**
     * {@inheritdoc}
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
     * @param \Platform\Media\Models\Media                        $media
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return void
     */
    public function uploaded(Media $media, UploadedFile $uploadedFile)
    {
        if ($thumbnail = $media->thumbnail) {
            \Illuminate\Support\Facades\File::delete($thumbnail);
        }

        $this->app['platform.media.manager']->handleUp($media);

        $this->flushCache($media);
    }

    /**
     * On updating event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function updating(Media $media)
    {
        // We need to verify if a file was uploaded while updating
    }

    /**
     * On updated event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function updated(Media $media)
    {
        $this->flushCache($media);
    }

    /**
     * On deleting event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function deleting(Media $media)
    {
        $this->app['platform.media.manager']->handleDown($media);
    }

    /**
     * On deleted event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function deleted(Media $media)
    {
        $this->flushCache($media);
    }

    /**
     * Flush the cache.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    protected function flushCache(Media $media)
    {
        $cache = $this->app['cache'];

        $cache->forget('platform.media.'.$media->id);

        $cache->forget('platform.media.path.'.$media->path);
    }
}
