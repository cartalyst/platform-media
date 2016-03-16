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
 * @version    3.2.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
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

    protected static $mediaIds;

    /**
     * Boot the media trait for a model.
     *
     * @return void
     */
    public static function bootMediaTrait()
    {
        static::saving(function($model) {
            if ($model->media_ids) {
                $mediaIds = is_array($model->media_ids) ? $model->media_ids : json_decode($model->media_ids);

                static::setMediaIds($mediaIds);

                unset($model->media_ids);

                if ($model->exists) {
                    $model->media()->sync($mediaIds);
                }
            }
        });

        static::saved(function($model) {
            if ($mediaIds = static::getMediaIds()) {
                $model->media()->sync($mediaIds);
            }
        });
    }

    public static function setMediaIds($ids)
    {
        static::$mediaIds = $ids;
    }


    public static function getMediaIds()
    {
        return static::$mediaIds;
    }

    /**
     * Retrieve all associated media records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media()
    {
        return $this->morphToMany(static::$mediaModel, 'object', 'media_relations');
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
}
