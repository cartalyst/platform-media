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
 * @version    4.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Models;

use InvalidArgumentException;
use Cartalyst\Tags\TaggableTrait;
use Cartalyst\Tags\TaggableInterface;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Media extends Model implements TaggableInterface
{
    use NamespacedEntityTrait, TaggableTrait;

    /**
     * {@inheritdoc}
     */
    public $table = 'media';

    /**
     * {@inheritdoc}
     */
    protected $appends = [
        'preset_paths',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'mime',
        'name',
        'path',
        'size',
        'private',
        'is_image',
        'extension',
        'width',
        'height',
        'roles',
        'namespace',
        'description',
    ];

    /**
     * {@inheritdoc}
     */
    protected static $entityNamespace = 'platform/media';

    /**
     * The Eloquent media relation model name.
     *
     * @var string
     */
    protected static $mediaRelationModel = 'Platform\Media\Models\MediaRelation';

    /**
     * Holds available media presets.
     *
     * @var array
     */
    protected static $presets = [];

    /**
     * Returns the media relation model.
     *
     * @return string
     */
    public static function getMediaRelationModel()
    {
        return static::$mediaRelationModel;
    }

    /**
     * Sets the media relation model.
     *
     * @param  string  $model
     * @return void
     */
    public static function setMediaRelationModel($model)
    {
        static::$mediaRelationModel = $model;
    }

    /**
     * Sets media presets.
     *
     * @param  array  $presets
     * @return void
     */
    public static function setPresets(array $presets)
    {
        static::$presets = $presets;
    }

    /**
     * Returns media presets.
     *
     * @return array
     */
    public static function getPresets()
    {
        return static::$presets;
    }

    /**
     * Get mutator for the "roles" attribute.
     *
     * @param  mixed  $roles
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getRolesAttribute($roles)
    {
        if (! $roles) {
            return [];
        }

        if (is_array($roles)) {
            return $roles;
        }

        if (! $_roles = json_decode($roles, true)) {
            throw new InvalidArgumentException("Cannot JSON decode roles [{$roles}].");
        }

        return $_roles;
    }

    /**
     * Set mutator for the "roles" attribute.
     *
     * @param  array  $roles
     * @return void
     */
    public function setRolesAttribute($roles)
    {
        // If we get a string, let's just ensure it's a proper JSON string
        if (! is_array($roles)) {
            $roles = $this->getRolesAttribute($roles);
        }

        if (! empty($roles)) {
            $roles                     = array_values(array_map('intval', $roles));
            $this->attributes['roles'] = json_encode($roles);
        } else {
            $this->attributes['roles'] = '';
        }
    }

    /**
     * Set mutator for the "namespace" attribute.
     *
     * @param  string  $namespace
     * @return void
     */
    public function setNamespaceAttribute($namespace)
    {
        if (! empty($namespace)) {
            $this->attributes['namespace'] = $namespace;
        }
    }

    /**
     * Preset Paths mutator.
     *
     * @return array
     */
    public function getPresetPathsAttribute()
    {
        $presets = [];

        foreach (array_keys(static::$presets) as $preset) {
            $presets[$preset] = getImagePath($this, $preset);
        }

        return $presets;
    }

    /**
     * Retrieve all associated media records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function relations()
    {
        return $this->hasMany(static::$mediaRelationModel);
    }
}
