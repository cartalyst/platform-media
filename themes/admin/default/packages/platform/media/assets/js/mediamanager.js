;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {
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

			self.dropzone.on('success', function(file) {

				self.dropzone.removeFile(file);

				self.opt.onSuccess();

			});

			self.dropzone.on('complete', function(file) {

				self.dropzone.processQueue();

				self.opt.onComplete();

			});

			$document.on('click', '[data-media-upload]', function(e) {

				e.preventDefault();

				self.dropzone.processQueue();

			});

			$document.on('click', '[data-media-delete]', function(e) {

				e.preventDefault();

				var id = $(this).data('media-delete');

				self.deleteMedia(id);

			});

			/*$('#checkAll').click(function() {

				$('input:checkbox').not(this).prop('checked', this.checked);

				$('#delete-selected').prop('disabled', ! this.checked);

			});*/

			$(document).on('click', '.selectedId', function(i, v){

				var checkCount = $('input:checkbox').length - 1;

				$('#checkAll').prop('checked',$('.selectedId:checked').length  == checkCount);

				var checked = $('.selectedId:checked').length > 0 ? false : true;

				if (checked)
				{
					$('[data-media-delete-box]').addClass('hide');
				}
				else
				{
					$('[data-media-delete-box]').removeClass('hide');
				}

				$('#delete-selected').prop('disabled', checked);

			});

			$(document).on('click', '[data-media-name]', function() {

				var id = $(this).data('media-name');

				$('[data-media="' + id + '"]').addClass('on_edit');

				$('[data-media-thumb="' + id + '"]').addClass('hide');

				$('[data-media-name="' + id + '"]').addClass('hide');

				$('[data-media-form="' + id + '"]').removeClass('hide');

			});

			/*
			$(document).on('click', function() {

				$('.on_edit').each(function() {

					var id = $(this).data('media');

					$(this).removeClass('on_edit');

					$('[data-media-thumb="' + id + '"]').removeClass('hide');

					$('[data-media-form="' + id + '"]').addClass('hide');

				});

			});
			*/

			$(document).on('click', '[data-media-delete-selected]', function(e) {

				e.preventDefault();

				$("input:checkbox[name=media]:checked").each(function()
				{
					self.deleteMedia($(this).val());
				});

			});

		},

		deleteMedia : function(id) {

			// Avoid scope issues
			var self = this;

			$.ajax({
				type: "GET",
				url: 'media/' + id + '/delete',
				success: function()
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
