<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	/*
	|--------------------------------------------------------------------------
	| File dispersion
	|--------------------------------------------------------------------------
	|
	| This feature allows you to have a better and more organized file
	| structure that you dictate using placeholders.
	|
	| To disable this feature just set a "false" boolean as value.
	|
	| Supported Placeholders:
	|
	|	Current Year
	|     :yyyy  ->  2013
	|     :yy    ->  13
	|
	|	Current Month
	|     :mmmm  ->  November
	|     :mmm   ->  Nov
	|     :mm    ->  11
	|
	|	current(array) Day
	|     :dddd  ->  Friday
	|     :ddd   ->  Fri
	|     :dd    ->  24
	|
	| Example:
	|
	|	'dispersion' => ':yyyy/:mm'
	|
	|	All your media files will be stored on:
	|
	|		public/media/2014/01/foo.jpg
	|		public/media/2014/01/bar.png
	|		public/media/2014/01/test.pdf
	|
	*/

	'dispersion' => false,

	/*
	|--------------------------------------------------------------------------
	| Maximum allowed size for uploaded files
	|--------------------------------------------------------------------------
	|
	| Define here the maximum size of an uploaded file in MegaBytes.
	|
	*/

	'maxFilesize' => 10,

	/*
	|--------------------------------------------------------------------------
	| Allowed types of files
	|--------------------------------------------------------------------------
	|
	| Specify here all the allowed mime types that can be uploaded.
	|
	| Look at http://www.iana.org/assignments/media-types for a
	| complete list of standard MIME types
	|
	*/

	'allowed' => array(

		// Audio & Video
		'audio/ogg', 'video/mp4', 'video/ogg',

		// Application
		'application/zip', 'application/pdf',

		// Images
		'image/gif', 'image/jpeg', 'image/png',

		// Text
		'text/plain',

	),

);
