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
 * @version    4.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Widgets;

use Platform\Media\Repositories\MediaRepositoryInterface;
use Cartalyst\Support\Contracts\NamespacedEntityInterface;

class Media
{
    /**
     * Constructor.
     *
     * @param  \Platform\Media\Repositories\MediaRepositoryInterface  $media
     * @return void
     */
    public function __construct(MediaRepositoryInterface $media)
    {
        $this->media = $media;
    }

    /**
     * Returns the given media path or the HTML <img> tag.
     *
     * @param  int  $id
     * @param  string|array  $name
     * @param  array  $attributes
     * @return string
     */
    public function path($id, $name = null, array $attributes = [])
    {
        $media = $this->media->find((int) $id);

        if (! $media) {
            return;
        }

        if (! $name) {
            return route('media.view', $media->path);
        }

        return getImagePath($media, $name, $attributes);
    }

    /**
     * Returns the media upload widget.
     *
     * @param  \Cartalyst\Support\Contracts\NamespacedEntityInterface|string  $namespace
     * @param  bool  $multiUpload
     * @param  string  $view
     * @return string
     */
    public function upload($namespace, $multiUpload = true, $view = '')
    {
        $isNamespaced = $namespace instanceof NamespacedEntityInterface;

        $mimes = $this->prepareMimes();

        $model = $isNamespaced ? $namespace : null;

        $currentUploads = $isNamespaced ? $model->media->sortBy('pivot.sort') : [];

        $uploadedMimeTypes = app('platform.media')->lists('mime')->unique()->toArray();

        $namespace = $isNamespaced ? $namespace->getEntityNamespace() : (string) $namespace;

        $view = $view ?: 'platform/media::widgets.upload';

        return view($view, compact(
            'model', 'namespace', 'multiUpload', 'mimes', 'currentUploads', 'uploadedMimeTypes'
        ));
    }

    /**
     * Prepares a mime types list.
     *
     * @return string
     */
    protected function prepareMimes()
    {
        return implode(', ', array_map(function ($el) {
            return last(explode('/', $el));
        }, $this->media->getAllowedMimes()));
    }
}
