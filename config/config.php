<?php


return [

	'email' => [

		// Total of allowed attachments per email
		'max_attachments' => 10,

		// Limit of the attachments that we'll be sending on the email
		'attachments_max_size' => 10485760, // 10 mb
		
		// default subject line to use if none was specified
		'subject' => 'You\'ve Got Media!'

	],

];
