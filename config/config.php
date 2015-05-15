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
 * @version    1.0.5
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Media\Styles\Style;
use Platform\Media\Styles\Manager;

return [

	#'default_style' => 'thumbnail',

	/*
	|--------------------------------------------------------------------------
	| Styles
	|--------------------------------------------------------------------------
	|
	| Define here the required Style config sets that will
	| be executed whenever a file gets uploaded.
	|
	*/

	'styles' => function(Manager $manager)
	{
		$manager->setStyle('thumbnail', function(Style $style)
		{
			// Set the style image width.
			$style->width = 300;

			// Set the style macros
			$style->macros = [ 'resize' ];

			// Set the storage path
			$style->path = public_path('cache/media');
		});
	},

    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    |
    | Define here the cache headers
    |
    */

    'headers' => [
        'max-age' => 2592000,
    ],

	/*
	|--------------------------------------------------------------------------
	| Macros
	|--------------------------------------------------------------------------
	|
	| Define here the Macros that can be used with the Style config sets.
	|
	*/

	'macros' => function(Manager $manager)
	{
		$manager->setMacro('resize', 'Platform\Media\Styles\Macros\ResizeMacro');
	}

];
