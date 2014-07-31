<?php
/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Media extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [

	'email' => [

		// Total of allowed attachments per email
		'max_attachments' => 10,

		// Limit of the attachments that we'll be sending on the email
		'attachments_max_size' => 10485760, // 10 mb

		// Default email subject
		'subject' => "You've Got Media!",

	],

	'styles' => [

		'thumbnail' => [

			'mime-type' => [
				'image/gif', 'image/jpeg', 'image/png',
			],
			'width'   => 40,
			'height'  => 40,
			'filters' => [
				'Platform\Media\Filters\ResizeFilter',
				'Platform\Media\Filters\ReduceQuality', # this is for testing
			],

		],

	],

];
