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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Media\Validator;

use Cartalyst\Support\Validator;

class MediaValidator extends Validator implements MediaValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    protected $rules = [
        'name' => 'required',
    ];

    /**
     * {@inheritdoc}
     */
    public function onUpdate()
    {
    }
}
