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
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

 ;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults =
	{
		onFail : function() {},
		onFileQueued : function() {},
		onSuccess : function() {},
		onComplete : function() {},
		onRemove : function() {},
		icons: {
			def:   'fa-file-o',
			image: 'fa-file-image-o',
			audio: 'fa-file-audio-o',
			video: 'fa-file-video-o',
			pdf:   'fa-file-pdf-o',
			zip:   'fa-file-zip-o',
		},
	};

	function MediaManager(options)
	{
		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		//
		this.files = {};

		//
		this.totalFiles = 0;

		//
		this.totalSize = 0;

		// Initialize the Media Manager
		this.initializer();
	}

	MediaManager.prototype =
	{
		/**
		 * Initializes the Media Manager.
		 *
		 * @return void
		 */
		initializer : function()
		{
			// Avoid scope issues
			var self = this;

			// Initialize the event listeners
			self.events();
		},

		events : function()
		{
			// Avoid scope issues
			var self = this;

			// Disable the upload button
			if (self.hasFiles() === false) self.disableUploadButton();

			// Process the queued files
			$(document).on('click', '[data-media-upload]', function()
			{
				self.processQueue();
			});

			//
			$(document).on('change', 'input[type="file"]', function(e)
			{
				FileAPI.reset(e.currentTarget);

				FileAPI.each(FileAPI.getFiles(e), function(file)
				{
					// add some sort of file validation..

					self.addFile(file);

					self.opt.onFileQueued(file);
				});

				if (self.hasFiles() === true) self.enableUploadButton();

				self.refreshTotals();
			});

			//
			$(document).on('click', '[data-media-remove]', function(e)
			{
				e.preventDefault();

				self.removeFile(
					$(this).data('media-remove')
				);

				self.opt.onRemove(self, $(this));

				self.refreshTotals();
			});
		},

		getUploadUrl : function()
		{
			return this.opt.uploadUrl;
		},

		setUploadUrl : function(url)
		{
			this.opt.uploadUrl = url;
		},

		refreshTotals : function()
		{
			$('[data-media-total-size]').html(
				(this.totalSize/FileAPI.KB).toFixed(2)
				);

			$('[data-media-total-files]').html(this.totalFiles);
		},

		hasFiles : function()
		{
			return ! $.isEmptyObject(this.files);
		},

		disableUploadButton : function()
		{
			$('[data-media-upload]').attr('disabled', true);
		},

		enableUploadButton : function()
		{
			$('[data-media-upload]').attr('disabled', false);
		},

		addFile : function(file)
		{
			// Avoid scope issues
			var self = this;

			self.files[FileAPI.uid(file)] = file;

			var data = {
				'file' : file,
				'icon' : self.opt.icons
			};

			var template = _.template($('[data-media-file-template]').html());

			$('[data-media-queue-list]').append(
				template(data)
			);

			if (/^image/.test(file.type))
			{
				var imageSize = self._getEl(file, '[data-media-file-image]').data('media-file-image');

				FileAPI.Image(file).preview(imageSize).rotate('auto').get(function(err, img)
				{
					if ( ! err )
					{
						self._getEl(file, '[data-media-file-image]').addClass('media-file__left_border').html(img);
					}
				});
			}

			self.totalFiles += 1;

			self.totalSize += file.size;
		},

		removeFile : function(id)
		{
			var file = this.files[id];

			this.totalFiles -= 1;

			this.totalSize -= file.size;

			delete this.files[id];

			$('[data-media-file="' + id + '"]').remove();
		},

		upload : function(fileId, file)
		{
			// Avoid scope issues
			var self = this;

			if (file)
			{
				var fileId = FileAPI.uid(file);

				file.xhr = FileAPI.upload(
				{
					url: self.opt.uploadUrl,
					files: { file : file },
					data: {
						name : self._getEl(file, 'input[name="' + fileId + '_name"]').val(),
						tags : self._getEl(file, 'input[name="' + fileId + '_tags[]"]').val(),
					},
					headers: {
						'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content')
					},
					upload: function()
					{
						self._getEl(file, '.file-ready').hide();
					},
					progress: function(evt)
					{
						self._getEl(file, '.file-progress').show();
					},
					complete: function(err, xhr)
					{
						var state = err ? 'error' : 'done';

						if (state === 'done')
						{
							self._getEl(file, '.file-progress').hide();

							self._getEl(file, '.file-success').show();

							// Timeout to show the success button for 200ms
							setTimeout(function()
							{
								self.opt.onSuccess();

								self.removeFile(fileId);

								if (self.hasFiles() === false) {
									self.disableUploadButton();
									self.opt.onComplete();
								}

								self.refreshTotals();
							}, 200);
						}
						else if (state === 'error')
						{

							self._getEl(file, '.file-progress').hide();

							self._getEl(file, '.file-error').show();

							self._getEl(file, '.file-error-help').text(state + ': '+ (err ? (xhr.statusText || err) : state));

							self.opt.onFail(xhr);

						}
					}
				});
			}
		},

		processQueue : function()
		{
			// Avoid scope issues
			var self = this;

			// Loop through all the files on the queue
			$.each(self.files, function(id, file)
			{
				self.upload(id, file);
			});
		},

		_getEl : function(file, sel)
		{
			var $el = $('[data-media-file=' + FileAPI.uid(file) + ']');

			return  sel ? $el.find(sel) : $el;
		},

	}

	$.mediamanager = function(options)
	{
		return new MediaManager(options);
	};

})(jQuery, window, document);
