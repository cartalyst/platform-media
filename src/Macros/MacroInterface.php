<?php

/*
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
 * @version    11.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Macros;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Platform\Media\Styles\Preset;

interface MacroInterface
{
    /**
     * Returns the Preset.
     *
     * @return \Platform\Media\Styles\Preset
     */
    public function getPreset();

    /**
     * Sets the Preset.
     *
     * @param \Platform\Media\Styles\Preset $preset
     *
     * @return $this
     */
    public function setPreset(Preset $preset);

    /**
     * Executes the macro.
     *
     * @return void
     */
    public function up(Media $media, File $file);

    /**
     * Reverts the executed macro.
     *
     * @return void
     */
    public function down(Media $media, File $file);
}
