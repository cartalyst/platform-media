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
 * @version    3.2.2
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
     * @param  string|null  $type
     * @return string
     */
    public function show($id, $type = null)
    {
        if ($media = $this->media->find((int) $id)) {
            switch ($type) {
                case 'thumbnail':

                    return url($media->thumbnail);

                case 'download':

                    return route('media.download', $media->path);

                default:

                    return route('media.view', $media->path);
            }
        }
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

        $currentUploads = $isNamespaced ? $model->media : [];

        $namespace = $isNamespaced ? $namespace->getEntityNamespace() : (string) $namespace;

        $options = compact('model', 'namespace', 'multiUpload', 'mimes', 'currentUploads');

        $view = $view ?: 'platform/media::widgets.upload';

        return view($view, $options);
    }

    /**
     * Returns the given media thumbnail in a <img> tag.
     *
     * @param  int  $id
     * @param  array  $options
     * @param  string|null  $default
     * @return string
     */
    public function thumbnail($id, array $options = [], $default = null)
    {
        if ($media = $this->media->find($id)) {
            $path = $media->is_image ? url($media->thumbnail) : $default;

            return '<img src="'.$path.'"'.implode(' ', $options).'>';
        }
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
