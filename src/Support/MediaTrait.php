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
 * @version    6.0.9
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Support;

trait MediaTrait
{
    /**
     * The Eloquent media model name.
     *
     * @var string
     */
    protected static $mediaModel = 'Platform\Media\Models\Media';

    /**
     * Related media ids.
     *
     * @var array
     */
    protected static $mediaIds;

    /**
     * Boot the media trait for a model.
     *
     * @return void
     */
    public static function bootMediaTrait()
    {
        static::creating(function ($model) {
            if ($mediaIds = request()->input('_media_ids')) {
                request()->replace(request()->except('_media_ids'));

                $mediaIds = is_array($mediaIds) ? $mediaIds : json_decode($mediaIds);
                $preparedMediaIds = [];
                foreach ($mediaIds as $key => $id) {
                    $preparedMediaIds[$id] = ['sort' => $key];
                }

                static::setMediaIds($preparedMediaIds);
            }
        });

        static::updating(function ($model) {
            if ($mediaIds = request()->input('_media_ids')) {
                request()->replace(request()->except('_media_ids'));

                $mediaIds = is_array($mediaIds) ? $mediaIds : json_decode($mediaIds);
                $preparedMediaIds = [];
                foreach ($mediaIds as $key => $id) {
                    $preparedMediaIds[$id] = ['sort' => $key];
                }

                static::setMediaIds($preparedMediaIds);
            }
        });

        static::saving(function ($model) {
            if ($mediaIds = request()->input('_media_ids')) {
                request()->replace(request()->except('_media_ids'));

                $mediaIds = is_array($mediaIds) ? $mediaIds : json_decode($mediaIds);
                $preparedMediaIds = [];
                foreach ($mediaIds as $key => $id) {
                    $preparedMediaIds[$id] = ['sort' => $key];
                }

                static::setMediaIds($preparedMediaIds);
            }
        });

        static::updated(function ($model) {
            if ($mediaIds = static::getMediaIds()) {
                $model->media()->sync($mediaIds);
            }
        });

        static::saved(function ($model) {
            if ($mediaIds = static::getMediaIds()) {
                $model->media()->sync($mediaIds);
            }
        });

        static::updated(function ($model) {
            if ($mediaIds = static::getMediaIds()) {
                $model->media()->sync($mediaIds);
            }
        });

        static::deleting(function ($model) {
            $model->media()->detach();
        });
    }

    /**
     * Retrieve all associated media records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media()
    {
        return $this->morphToMany(static::$mediaModel, 'object', 'media_relations')->withPivot('sort');
    }

    /**
     * {@inheritdoc}
     */
    public static function getMediaModel()
    {
        return static::$mediaModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function setMediaModel($model)
    {
        static::$mediaModel = $model;
    }

    /**
     * Sets related media ids.
     *
     * @param  array  $ids
     * @return void
     */
    public static function setMediaIds(array $ids)
    {
        static::$mediaIds = $ids;
    }

    /**
     * Returns related media ids.
     *
     * @return array
     */
    public static function getMediaIds()
    {
        return static::$mediaIds;
    }
}
