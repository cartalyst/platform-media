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

        $this->defaultMacro = $config['defaultMacro'];
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
        // Get the uploaded file mime type
        $mimeType = $file->getMimeType();

        // Loop through all the registered presets
        foreach ($this->getPresets() as $name => $attributes) {
            // Initialize the preset
            $preset = new Preset($name, $attributes);

            // Check if the mime type of the uploaded file is allowed for this preset
            if (! empty($preset->mimes) && ! in_array($mimeType, $preset->mimes)) {
                continue;
            }

            // If this preset doesn't have any macros,
            // we will then use the default macro.
            if (empty($macros = $preset->macros)) {
                $default = $this->defaultMacro;

                $macros = [ $default => $this->macros[$default] ];
            }

            // Loop through the preset macros
            foreach ($macros as $name => $attributes) {
                $macro = array_get($this->getMacros(), $attributes);
                # need to make sure we detect invalid macros

                $macro->setPreset($preset)->{$method}($media, $file);
            }
        }
    }
}
