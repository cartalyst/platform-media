;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {

	};

	function MediaManagerNew(manager, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		// Create a language dictionary
		this.langDict = defaults.languages;

		// Cache the form selector
		this.$form = manager;

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

			$('input[type="file"]').on('change', function(e)
			{
				var files = FileAPI.getFiles(e);

				prepareForUpload(files);

				FileAPI.reset(e.currentTarget);
			});

			var FU = {
				icon: {
					  def:   '//cdn1.iconfinder.com/data/icons/CrystalClear/32x32/mimetypes/unknown.png'
					, image: '//cdn1.iconfinder.com/data/icons/humano2/32x32/apps/synfig_icon.png'
					, audio: '//cdn1.iconfinder.com/data/icons/august/PNG/Music.png'
					, video: '//cdn1.iconfinder.com/data/icons/df_On_Stage_Icon_Set/128/Video.png'
				},

				files: [],
				index: 0,
				active: false,

				add: function(file)
				{
					FU.files.push(file);

					if (/^image/.test(file.type))
					{
						FileAPI.Image(file).preview(35).rotate('auto').get(function(err, img)
						{
							if( ! err )
							{
								FU._getEl(file, '.js-left').addClass('b-file__left_border').html(img);
							}
						});
					}
				},

				getFileById: function(id)
				{
					var i = FU.files.length;

					while (i--)
					{
						if (FileAPI.uid(FU.files[i]) == id)
						{
							return FU.files[i];
						}
					}
				},

				start: function()
				{
					if ( ! FU.active && (FU.active = FU.files.length > FU.index))
					{
						FU._upload(FU.files[FU.index]);
					}
				},

				abort: function(id)
				{
					var file = this.getFileById(id);

					if (file.xhr)
					{
						file.xhr.abort();
					}
				},

				_getEl: function(file, sel)
				{
					var $el = $('#file-'+FileAPI.uid(file));

					return  sel ? $el.find(sel) : $el;
				},

				_upload: function(file)
				{
					if (file)
					{
						file.xhr = FileAPI.upload(
						{
							url: self.opt.uploadUrl,
							files: { file: file },
							data: { data : FU._getEl(file, ':input').serialize() },
							upload: function()
							{
								FU._getEl(file).addClass('b-file_upload');
								FU._getEl(file, '.js-progress').css({ opacity: 0 }).show().animate({ opacity: 1 }, 100);
							},
							progress: function(evt)
							{
								FU._getEl(file, '.js-bar').css('width', evt.loaded/evt.total * 100 + '%');
							},
							complete: function(err, xhr)
							{
								var state = err ? 'error' : 'done';

								FU._getEl(file).removeClass('b-file_upload');
								FU._getEl(file, '.js-progress').animate({ opacity: 0 }, 200, function (){ $(this).hide() });
								FU._getEl(file, '.js-info').append(', <b class="b-file__'+state+'">'+(err ? (xhr.statusText || err) : state)+'</b>');

								FU.index++;
								FU.active = false;

								FU.start();
							}
						});
					}
				}
			};

			function prepareForUpload(files)
			{
				var $Queue = $('<div/>').prependTo('#preview');

				FileAPI.each(files, function (file){
					if( file.size >= 25*FileAPI.MB ){
						alert('Sorrow.\nMax size 25MB')
					}
					else if( file.size === void 0 ){
						$('#oooops').show();
						$('#buttons-panel').hide();
					}
					else {
						$Queue.append(tmpl($('#b-file-ejs').html(), { file: file, icon: FU.icon }));

						FU.add(file);
					}
				});
			}

			$('.test').on('click', function()
			{
				FU.start();
			});

			$('.test').on('click', function()
			{
				FU.start();
			});

		}

	}

	$.mediamanager = function(manager, options) {

		return new MediaManagerNew(manager, options);

	};

})(jQuery, window, document);
