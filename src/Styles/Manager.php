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
 * @version    4.0.3
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

class Manager implements ManagerInterface
{
    /**
     * The filesystem instance.
     *
     * @var \Cartalyst\Filesystem\Filesystem
     */
    protected $filesystem;

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
     * The media repository.
     *
     * @var \Platform\Media\Repositories\MediaRepositoryInterface
     */
    protected $media;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->filesystem = $app['cartalyst.filesystem'];

        $this->media = $app['platform.media'];
    }

    /**
     * {@inheritDoc}
     */
    public function getPresets()
    {
        return $this->presets;
    }

    /**
     * {@inheritDoc}
     */
    public function setPreset($preset, array $info = [])
    {
        if ($preset instanceof Preset) {
            $this->presets[$preset->name] = $preset;
        } else {
            $this->presets[$preset] = new Preset($preset, $info);
        }

        $this->setModelPresets();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMacros()
    {
        return $this->macros;
    }

    /**
     * {@inheritDoc}
     */
    public function setMacro($name, $class)
    {
        $this->macros[$name] = $class;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function handleUp(Media $media)
    {
        $this->applyPresets('up', $media);
    }

    /**
     * {@inheritDoc}
     */
    public function handleDown(Media $media)
    {
        $this->applyPresets('down', $media);
    }

    /**
     * {@inheritDoc}
     */
    public function isValidPreset($name)
    {
        return array_key_exists($name, $this->presets);
    }

    /**
     * {@inheritDoc}
     */
    public function applyPreset($name, $direction, Media $media)
    {
        // Initialize the preset
        $preset = $this->getPreset($name);

        // Set the media entity
        $preset->setMedia($media);

        // Set the media file object
        $preset->setFile(
            $this->filesystem->get($media->path)
        );

        // Check if the preset is in a valid state
        if ($preset->isValid()) {
            // Store all the available macros
            $preset->availableMacros = $this->macros;

            // Apply the macros on this preset
            $preset->applyMacros($direction);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function applyPresets($direction, Media $media)
    {
        foreach ($this->getPresets() as $name => $attributes) {
            $this->applyPreset($name, $direction, $media);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPreset($name)
    {
        return $this->getPresets()[$name];
    }

    /**
     * Updates the presets on the model.
     *
     * @return void
     */
    protected function setModelPresets()
    {
        $this->media->createModel()->setPresets($this->presets);
    }
}
