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
		token : null,
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

			$document.on('submit', 'form', function() {

				var id = $(this).data('media-form');

				var data = $(this).serializeArray();
				data.push({
					name  : '_token',
					value : self.opt.token,
				});

				$.ajax({
					type : 'POST',
					data : data,
					url : self.opt.updateUrl.replace(':id', id),
					success : function()
					{
						self.opt.onSuccess();
					}
				});

				return false;

			});

			$document.on('click', '.selectedId', function(e) {

				e.stopPropagation();

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

			});

			$document.on('click', function() {

				self.closeMediaForms();

			});

			$document.on('click', '[data-media-name]', function(e) {

				e.stopPropagation();

				var id = $(this).data('media-name');

				self.closeMediaForms();

				self.showMediaForm(id);

			});

			$document.on('click', '[data-media-form]', function(e) {

				e.stopPropagation();

			});

			$document.on('click', '[data-media-delete-selected]', function(e) {

				e.preventDefault();

				$('input:checkbox[name=media]:checked').each(function()
				{
					self.deleteMedia($(this).val());
				});

			});

		},

		closeMediaForms : function() {

			// Avoid scope issues
			var self = this;

			$('.on_edit').each(function() {

				var id = $(this).data('media');

				self.closeMediaForm(id);

			});

		},

		closeMediaForm : function(id) {

			$('[data-media="' + id + '"]').removeClass('on_edit');

			$('[data-media-file="' + id + '"]').removeClass('hide');

			$('[data-media-form="' + id + '"]').addClass('hide');

		},

		showMediaForm : function(id) {

			$('[data-media="' + id + '"]').addClass('on_edit');

			$('[data-media-file="' + id + '"]').addClass('hide');

			$('[data-media-form="' + id + '"]').removeClass('hide').find('.name').focus();

		},

		deleteMedia : function(id) {

			// Avoid scope issues
			var self = this;

			$.ajax({
				type : 'POST',
				data : { '_token' : self.opt.token },
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
