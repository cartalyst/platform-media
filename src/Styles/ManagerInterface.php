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
 * @version    5.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Styles;

use Platform\Media\Models\Media;

interface ManagerInterface
{
    /**
     * Returns all the registered presets.
     *
     * @return array
     */
    public function getPresets();

    /**
     * Sets a new preset.
     *
     * @param  string|\Platform\Media\Styles\Preset  $preset
     * @param  array  $info
     * @return $this
     */
    public function setPreset($preset, array $info = []);

    /**
     * Returns all the registered macros.
     *
     * @return array
     */
    public function getMacros();

    /**
     * Sets a new macro.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function setMacro($name, $class);

    /**
     * Handles the presets on upload.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function handleUp(Media $media);

    /**
     * Handles the presets on delete.
     *
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function handleDown(Media $media);

    /**
     * Determines if the given preset is valid.
     *
     * @param  string  $name
     * @return bool
     */
    public function isValidPreset($name);

    /**
     * Apply the preset on the given media.
     *
     * @param  string  $name
     * @param  string  $direction
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function applyPreset($name, $direction, Media $media);

    /**
     * Apply the presets on the given media.
     *
     * @param  string  $direction
     * @param  \Platform\Media\Models\Media  $media
     * @return void
     */
    public function applyPresets($direction, Media $media);

    /**
     * Returns the given preset instance.
     *
     * @param  string  $name
     * @return \Platform\Media\Styles\Preset
     */
    public function getPreset($name);
}
