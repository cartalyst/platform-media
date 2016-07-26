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
 * @version    4.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Styles;

use Closure;
use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Illuminate\Container\Container;

class Manager
{
    /**
     * The registered presets.
     *
     * @var array
     */
    protected $presets = [];

    /**
     * The registered macros.
     *
     * @var array
     */
    protected $macros = [];

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $config = $app['config']->get('platform-media');

        $this->macros = $config['macros'];

        $this->presets = $config['presets'];
    }

    /**
     * Returns all the registered presets.
     *
     * @return array
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * Sets a new preset.
     *
     * @param  string  $name
     * @param  array  $info
     * @return $this
     */
    public function setPreset($name, array $info)
    {
        $this->presets[$name] = $info;

        return $this;
    }

    /**
     * Returns all the registered macros.
     *
     * @return array
     */
    public function getMacros()
    {
        return $this->macros;
    }

    /**
     * Sets a new macro.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function setMacro($name, $class)
    {
        $this->macros[$name] = $class;

        return $this;
    }

    /**
     * Handles the presets on upload.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function handleUp(Media $media, File $file)
    {
        $this->applyPresets('up', $media, $file);
    }

    /**
     * Handles the presets on delete.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function handleDown(Media $media, File $file)
    {
        $this->applyPresets('down', $media, $file);
    }

    /**
     * Determines if the given preset is valid.
     *
     * @param  string  $name
     * @return bool
     */
    public function isValidPreset($name)
    {
        return array_key_exists($name, $this->presets);
    }

    /**
     * Apply the preset on the given media.
     *
     * @param  string  $name
     * @param  string  $direction
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function applyPreset($name, $direction, Media $media, File $file)
    {
        // Get the attributes of the given preset
        $attributes = $this->presets[$name];

        // Initialize the preset
        $preset = new Preset($name, $attributes);

        // Set the media entity
        $preset->setMedia($media);

        // Set the media file object
        $preset->setFile($file);

        // Check if the preset is in a valid state
        if ($preset->isValid()) {
            // Store all the available macros
            $preset->availableMacros = $this->macros;

            // Apply the macros on this preset
            $preset->applyMacros($direction);
        }
    }

    /**
     * Apply the presets on the given media.
     *
     * @param  string  $direction
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    public function applyPresets($direction, Media $media, File $file)
    {
        foreach ($this->getPresets() as $name => $attributes) {
            $this->applyPreset($name, $direction, $media, $file);
        }
    }
}
