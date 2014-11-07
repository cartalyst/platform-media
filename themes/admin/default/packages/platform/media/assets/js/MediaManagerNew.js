;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {

		icons: {
			  def:   '//cdn1.iconfinder.com/data/icons/CrystalClear/32x32/mimetypes/unknown.png'
			, image: '//cdn1.iconfinder.com/data/icons/humano2/32x32/apps/synfig_icon.png'
		},

	};

	function MediaManagerNew(manager, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		//
		this.files = {};

		// Initialize the Media Manager
		this.initializer();

	}

	MediaManagerNew.prototype = {

		/**
		 * Initializes the Media Manager.
		 *
		 * @return void
		 */
		initializer : function() {

			// Avoid scope issues
			var self = this;

			$(document).on('click', '[data-media-upload]', function()
			{
				self.processQueue();
			});

			$(document).on('change', 'input[type="file"]', function(e)
			{
				FileAPI.reset(e.currentTarget);

				var $Queue = $('<div/>').prependTo('#preview');

				FileAPI.each(FileAPI.getFiles(e), function(file)
				{
					$Queue.append(tmpl($('#b-file-ejs').html(),
					{
						file : file,
						icon : self.opt.icons
					}));

					self.addFile(file);
				});
			});

			$(document).on('click', '[data-media-remove]', function(e)
			{
				var id = $(this).data('media-remove');

				delete self.files.id;

				$('[data-media-file="' + id + '"]').remove();
			});

		},

		addFile : function(file)
		{
			var self = this;

			self.files[FileAPI.uid(file)] = file;

			if (/^image/.test(file.type))
			{
				FileAPI.Image(file).preview(35).rotate('auto').get(function(err, img)
				{
					if( ! err )
					{
						self._getEl(file, '.js-left').addClass('b-file__left_border').html(img);
					}
				});
			}
		},

		upload : function(file)
		{
			var self = this;

			if (file)
			{
				file.xhr = FileAPI.upload(
				{
					url: self.opt.uploadUrl,
					files: { file : file },
					data: {
						name : self._getEl(file, 'input[name="name"]').val(),
						tags : self._getEl(file, 'input[name="tags"]').val(),
					},
					headers: {
						'X-CSRF-Token' : $('meta[name="csrf-token"]').attr('content')
					},
					upload: function()
					{
						self._getEl(file).addClass('b-file_upload');
						self._getEl(file, '.js-progress').css({ opacity: 0 }).show().animate({ opacity: 1 }, 100);
					},
					progress: function(evt)
					{
						self._getEl(file, '.js-bar').css('width', evt.loaded/evt.total * 100 + '%');
					},
					complete: function(err, xhr)
					{
						var state = err ? 'error' : 'done';

						self._getEl(file, '.js-progress').animate({ opacity: 0 }, 200, function (){ $(this).hide() });
						self._getEl(file, '.js-info').append(', <b class="b-file__'+state+'">'+(err ? (xhr.statusText || err) : state)+'</b>');
					}
				});
			}
		},

		processQueue : function()
		{
			var self = this;

			$.each(self.files, function(id, file)
			{
				self.upload(file);
			});
		},

		_getEl : function(file, sel)
		{
			var $el = $('[data-media-file=' + FileAPI.uid(file) + ']');

			return  sel ? $el.find(sel) : $el;
		},

		getFileById: function(id)
		{
			var i = self.files.length;

			while (i--)
			{
				if (FileAPI.uid(self.files[i]) == id)
				{
					return self.files[i];
				}
			}
		},

		abort: function(id)
		{
			var file = this.getFileById(id);

			if (file.xhr)
			{
				file.xhr.abort();
			}
		}

	}

	$.mediamanager = function(manager, options) {

		return new MediaManagerNew(manager, options);

	};

})(jQuery, window, document);
