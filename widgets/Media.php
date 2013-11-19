<?php namespace Platform\Media\Widgets;

class Media {

	public function show($form = null, $allowedMimeTypes = null, $extension = null)
	{
		/*

			// Allow only and all the images mime types to be uploaded
			@widget(..., array('#myMediaForm', 'image/*'))

			// Allow only txt and png images
			@widget(..., array('#myMediaForm', 'image/png, text/plain'))

			// Associate uploaded images to an extension
			@widget(..., array('#myMediaForm', null, 'platform/users'))


			## will need an extra parameter, probably, so that we can associate
			## media to certain user groups, user ids, etc...
			## once the media get's uploaded we call a certain class method
			## would work similarly to the menu types....

		*/
	}

}
