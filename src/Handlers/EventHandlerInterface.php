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
 * @version    11.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Handlers;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface EventHandlerInterface extends BaseEventHandlerInterface
{
    /**
     * On upload event.
     *
     * @param \Platform\Media\Models\Media                        $media
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     *
     * @return void
     */
    public function uploaded(Media $media, UploadedFile $uploadedFile);

    /**
     * On deleting event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function deleting(Media $media);

    /**
     * On deleted event.
     *
     * @param \Platform\Media\Models\Media $media
     *
     * @return void
     */
    public function deleted(Media $media);
}
