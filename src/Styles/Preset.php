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

namespace Platform\Media\Styles;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use League\Flysystem\FileNotFoundException;

class Preset
{
    /**
     * The attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The media entity.
     *
     * @var \Platform\Media\Models\Media
     */
    protected $media;

    /**
     * The media file object.
     *
     * @var \Cartalyst\Filesystem\File
     */
    protected $file;

    /**
     * Constructor.
     *
     * @param  string  $name
     * @param  array  $attributes
     * @return void
     */
    public function __construct($name, array $attributes)
    {
        $this->attributes = array_merge(compact('name'), $attributes);
    }

    /**
     * Returns the media entity.
     *
     * @return \Platform\Media\Models\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the media entity.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return $this
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the media file object.
     *
     * @return \Cartalyst\Filesystem\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the media file object.
     *
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Determines if the preset is in a valid state
     * so we can apply the macros more safely.
     *
     * @return bool
     */
    public function isValid()
    {
        $mimes = $this->mimes;

        $namespaces = $this->namespaces;

        try {
            $mimeType = $this->file->getMimeType();
        } catch (FileNotFoundException $e) {
            return false;
        }

        $namespace = $this->media->namespace;

        if (! empty($mimes) && ! in_array($mimeType, $mimes)) {
            return false;
        }

        if (! empty($namespaces) && ! in_array($namespace, $namespaces)) {
            return false;
        }

        return true;
    }

    /**
     * Apply all the relevant macros on this preset.
     *
     * @param  string  $method
     * @return void
     */
    public function applyMacros($method = 'up')
    {
        foreach ($this->macros as $macro) {
            if (isset($this->availableMacros[$macro])) {
                $instance = app($this->availableMacros[$macro]);

                $instance->setPreset($this)->{$method}($this->media, $this->file);
            }
        }
    }

    /**
     * Accessor for the "path" attribute.
     *
     * @param  string  $path
     * @return string
     */
    public function getPathAttribute($path)
    {
        return $path ?: public_path('cache/media/'.$this->name);
    }

    /**
     * Accessor for the "constraints" attribute.
     *
     * @param  array  $constraints
     * @return array
     */
    public function getConstraintsAttribute($constraints)
    {
        return $constraints ?: [];
    }

    /**
     * Mutator for the "constraints" attribute.
     *
     * @param  array  $constraints
     * @return void
     */
    public function setConstraintsAttribute(array $constraints)
    {
        foreach (array_unique($constraints) as $constraint) {
            $this->attributes['constraints'][] = $constraint;
        }
    }

    /**
     * Accessor for the "mimes" attribute.
     *
     * @param  array  $mimes
     * @return array
     */
    public function getMimesAttribute($mimes)
    {
        return $mimes ?: [];
    }

    /**
     * Mutator for the "mimes" attribute.
     *
     * @param  array  $mimes
     * @return void
     */
    public function setMimesAttribute(array $mimes)
    {
        foreach (array_unique($mimes) as $mime) {
            $this->attributes['mimes'][] = $mime;
        }
    }

    /**
     * Accessor for the "macros" attribute.
     *
     * @param  array  $macros
     * @return array
     */
    public function getMacrosAttribute($macros)
    {
        return $macros ?: [];
    }

    /**
     * Mutator for the "macros" attribute.
     *
     * @param  array  $macros
     * @return void
     */
    public function setMacrosAttribute(array $macros)
    {
        foreach (array_unique($macros) as $macro) {
            $this->attributes['macros'][] = $macro;
        }
    }

    /**
     * Accessor for the "namespaces" attribute.
     *
     * @param  array  $namespaces
     * @return array
     */
    public function getNamespacesAttribute($namespaces)
    {
        return $namespaces ?: [];
    }

    /**
     * Mutator for the "namespaces" attribute.
     *
     * @param  array  $namespaces
     * @return void
     */
    public function setNamespacesAttribute(array $namespaces)
    {
        foreach (array_unique($namespaces) as $namespace) {
            $this->attributes['namespaces'][] = $namespace;
        }
    }

    /**
     * Dynamically retrieve attributes from the object.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $method = 'get'.ucfirst($key).'Attribute';

        $value = isset($this->attributes[$key]) ? $this->attributes[$key] : null;

        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Dynamically set attributes on the object.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $method = 'set'.ucfirst($key).'Attribute';

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            $this->attributes[$key] = $value;
        }
    }
}
