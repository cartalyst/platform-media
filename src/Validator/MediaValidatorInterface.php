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
 * @version    7.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2018, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Media\Validator;

interface MediaValidatorInterface
{
    /**
     * Updating a media scenario.
     *
     * @return void
     */
    public function onUpdate();
}
