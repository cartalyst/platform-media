;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {
		updateUrl : null,
		deleteUrl : null,
		onSuccess : function() {},
		onComplete : function() {},
		autoProcessQueue : false,
		addRemoveLinks : true,
		parallelUploads : 6,
		dictRemoveFile : 'Cancel',
		dictCancelUpload : 'Cancel',
		languages : {
			file : 'File',
			files : 'Files',
			inQueue : '<strong>:amount</strong> :files in the queue'
		}
	};

	function MediaManager(manager, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		// Create a language dictionary
		this.langDict = defaults.languages;

		// Cache the form selector
		this.$form = manager;

		// Initialize the Media Manager
		this.initializer();

	}

	MediaManager.prototype = {

		/**
		 * Initializes the Media Manager.
		 *
		 * @return void
		 */
		initializer : function() {

			// Avoid scope issues
			var self = this;

			// Prepare Dropzone
			self.dropzone = new Dropzone(self.$form, self.opt);

			// Initialize the event listeners
			this.events();

		},

		/**
		 * Initializes all the event listeners.
		 *
		 * @return void
		 */
		events : function() {

			// Avoid scope issues
			var self = this;

			var $document = $(document);

			var totalFiles = 0;

			var totalSize = 0;

			$('[data-media-total-files]').html(self.totalFiles(totalFiles));
			$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			self.dropzone.on('addedfile', function(file) {

				totalFiles += 1;

				totalSize += file.size;

				$('[data-media-total-files]').html(self.totalFiles(totalFiles));
				$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('removedfile', function(file) {

				totalFiles -= 1;

				totalSize -= file.size;

				$('[data-media-total-files]').html(self.totalFiles(totalFiles));
				$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('success', function(file, response) {

				self.dropzone.removeFile(file);

				self.opt.onSuccess(response);

			});

			self.dropzone.on('complete', function(file, response) {

				self.dropzone.processQueue();

				self.opt.onComplete(response);

			});

			$document.on('click', '[data-media-upload]', function(e) {

				e.preventDefault();

				self.dropzone.processQueue();

			});

			$document.on('click', '.media__actions', function(e) {

				e.stopPropagation();

			});

			$document.on('change', '#private', function() {

				if ($(this).val() == 1)
				{
					$('[data-media-groups]').removeClass('hide');
				}
				else
				{
					$('[data-media-groups]').addClass('hide');
				}

			});

			$document.on('click', '[data-media]', function() {

				var id = $(this).data('media');

				var media = $('#media_' + id);

				if (media.prop('checked') == false)
				{
					media.prop('checked', true);
					media.parent().addClass('media__select--checked');
				}
				else
				{
					media.prop('checked', false);
					media.parent().removeClass('media__select--checked');
				}

				var totalSelected = $('.media__select input:checked').length;

				$('[data-media-total-selected]').html(totalSelected);

				if (totalSelected > 0 ? false : true)
				{
					$('[data-media-sidebar]').addClass('hide');
				}
				else
				{
					$('[data-media-sidebar]').removeClass('hide');
				}

			});

			$document.on('click', '[data-media-update-selected]', function(e) {

				e.preventDefault();

				var data = {
					'private' : $('#private').val(),
					'groups'  : $('#groups').val()
				}

				$('input:checkbox[name=media]:checked').each(function()
				{
					self.updateMedia($(this).val(), data);
				});

				$('#private').val(0);
				$('#groups').val('');

			});

			$document.on('click', '[data-media-delete-selected]', function(e) {

				e.preventDefault();

				$('input:checkbox[name=media]:checked').each(function()
				{
					self.deleteMedia($(this).val());
				});

			});

		},

		updateMedia : function(id, data) {

			// Avoid scope issues
			var self = this;

			$.ajax({
				type : 'POST',
				url : self.opt.updateUrl.replace(':id', id),
				data : data,
				success : function()
				{
					self.opt.onSuccess();
				}
			});

		},

		deleteMedia : function(id) {

			// Avoid scope issues
			var self = this;

			$.ajax({
				type : 'POST',
				url : self.opt.deleteUrl.replace(':id', id),
				success : function()
				{
					self.opt.onSuccess();
				}
			});

		},

		totalFiles : function(totalFiles) {

			// Avoid scope issues
			var self = this;

			return self.langDict.inQueue
				.replace(':amount', totalFiles)
				.replace(':files', (totalFiles == 1 ? self.langDict.file : self.langDict.files));

		}

	}

	$.mediamanager = function(manager, options) {

		return new MediaManager(manager, options);

	};

})(jQuery, window, document);
