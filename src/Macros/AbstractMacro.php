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

namespace Platform\Media\Macros;

use Cartalyst\Filesystem\File;
use Platform\Media\Models\Media;
use Platform\Media\Styles\Preset;

abstract class AbstractMacro implements MacroInterface
{
    /**
     * The Preset object.
     *
     * @var \Platform\Media\Presets\Preset
     */
    protected $preset;

    /**
     * {@inheritdoc}
     */
    public function getPreset()
    {
        return $this->preset;
    }

    /**
     * {@inheritdoc}
     */
    public function setPreset(Preset $preset)
    {
        $this->preset = $preset;

        return $this;
    }
}
