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
 * @version    6.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Time to live
    |--------------------------------------------------------------------------
    |
    | Define here the time to live, in seconds, before the browser
    | sends another request to re-cache the media.
    |
    */

    'ttl' => 2592000,

    /*
    |--------------------------------------------------------------------------
    | Macros
    |--------------------------------------------------------------------------
    |
    | Define here the Macros that can be used with the Style config sets.
    |
    */

    'macros' => [

        'fit' => 'Platform\Media\Macros\Fit',

    ],

    /*
    |--------------------------------------------------------------------------
    | Presets
    |--------------------------------------------------------------------------
    |
    | Define here the image presets that should be generated upon upload.
    |
    */

    'presets' => [

        'thumb' => [
            'width' => 400,

            'macros' => [ 'fit' ],
        ],

        'medium' => [
            'width' => 800,

            'macros' => [ 'fit' ],
        ],

        '720p' => [
            'width'  => 1280,
            'height' => 720,

            'macros' => [ 'fit' ],
        ],

        '1080p' => [
            'width'  => 1920,
            'height' => 1080,

            'macros' => [ 'fit' ],
        ],

    ],

];
