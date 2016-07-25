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
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * Apply the presets on the given media.
     *
     * @param  string  $method
     * @param  \Platform\Media\Models\Media  $media
     * @param  \Cartalyst\Filesystem\File  $file
     * @return void
     */
    protected function applyPresets($method, Media $media, File $file)
    {
        // Loop through all the registered presets
        foreach ($this->getPresets() as $name => $attributes) {
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
                $preset->applyMacros();
            }
        }
    }
}
