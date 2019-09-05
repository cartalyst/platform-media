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
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Macros;

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
