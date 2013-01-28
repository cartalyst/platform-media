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

	// The media storage root directory in the file system,
	// this is relative to the 'public' directory.
	'directory' => 'platform/media',




	// Default validation config, for when
	// no other validation is passed. This associative
	// array is condensed into a string for validation,
	// so you can put any allowed validation rules in.
	'validation' => array(

		// Maximum file size
		'max' => 10240, // 10 MB

		// Allowed mimetypes for files. These
		// can be found in config/mimes.php
		// of your Platform application.
		'mimes' => array(

			// Images
			'jpeg', 'png', 'gif', 'bmp',

			// Files
			'pdf', 'zip', 'txt'
		)

	)

);
